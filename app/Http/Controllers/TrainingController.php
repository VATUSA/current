<?php

namespace App\Http\Controllers;

use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Helpers\AuthHelper;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\TrainingRecord;
use App\Models\OTSEval;
use App\Models\User;
use App\Models\Promotions;
use App\Models\Role;
use App\Models\OTSEvalForm;

class TrainingController extends Controller
{
    public
    function ajaxCanModifyRecord(
        $record
    )
    {
        $record = TrainingRecord::find($record);
        if (!$record->count()) {
            return response()->json(false);
        }

        return response()->json(Auth::check() && $record->student_id != Auth::user()->cid &&
            (AuthHelper::authACL()->isVATUSAStaff() || !in_array($record->ots_status, [1, 2])) &&
            (AuthHelper::authACL()->isFacilitySeniorStaff($record->facility) ||
                (AuthHelper::authACL()->isTrainingStaff($record->facility)
                    && $record->instructor_id == Auth::user()->cid)));
    }

    public
    function getOTSEval(
        Request $request,
        int     $cid,
                $form = null
    )
    {
        $student = User::find($cid);
        if (!$student) {
            abort(404);
        }
        $form = $form ? OTSEvalForm::has('perfcats')
            ->has('perfcats.indicators')->withAll()->find($form)
            : OTSEvalForm::active()->has('perfcats')->has('perfcats.indicators')
                ->withAll()->where('rating_id', $student->rating + 1)->first();
        $authACL = AuthHelper::authACL();
        if (!$authACL->canPromoteForFacility($student->facility, $student->rating + 1)) {
            abort(403);
        }
        if (!$student || !$form) {
            abort(404, "The Rating Exam evaluation form is invalid.");
        }
        if ($form->rating_id !== $student->rating + 1 || !$student->promotionEligible()) {
            return redirect('/mgt/facility#mem')->with('error', 'The controller is not eligible for that evaluation.');
        }

        return response()->view('mgt.controller.training.otsEval', compact('student', 'form'));

    }

    public
    function viewOTSEval(
        Request $request,
        int     $eval
    )
    {
        $eval = OTSEval::withAll()->find($eval);
        if (!$eval) {
            abort(404, "The Rating Exam evaluation form is invalid.");
        }
        $student = $eval->student;
        $authACL = AuthHelper::authACL();
        if (!$authACL->canViewTrainingRecords($student->facility)) {
            abort(403);
        }
        $attempt = Helper::numToOrdinalWord(OTSEval::where([
            'student_id' => $eval->student_id,
            ['exam_date', '<=', $eval->exam_date],
            ['exam_position', 'like', '%' . explode('_', $eval->exam_position)[1]]
        ])->count());
        $recs = TrainingRecord::where([
            'student_id' => $eval->student_id,
            ['session_date', '<=', $eval->exam_date],
            ['position', 'like', '%' . explode('_', $eval->exam_position)[1]],
            'ots_status' => 2
        ])->groupBy(['instructor_id'])->orderBy('session_date', 'desc')
            ->get()->pluck('instructor_id', 'session_date');

        return response()->view('mgt.controller.training.viewOtsEval',
            compact('student', 'eval', 'attempt', 'recs'));
    }

    public
    function viewTrainingStatistics(
        Request $request
    )
    {
        function time_to_seconds($str_time): int
        {
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        }

        function seconds_to_string($seconds): string
        {
            if ($seconds == 0) {
                return "No sessions";
            }
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds / 60) % 60);
            if (!$hours) {
                return $minutes . " minute" . ($minutes !== 1 ? 's' : '');
            } else {
                return "$hours hour" . ($hours !== 1 ? 's' : '') . ", " . $minutes . " minute" .
                    ($minutes !== 1 ? 's' : '');
            }
        }
        $authACL = AuthHelper::authACL();
        if (!$authACL->canViewTrainingRecords()) {
            abort(403);
        }

//        abort(500); // Disable training statistics as it's overloading the nodes, will re-enable after performance rework
        ini_set('memory_limit', '512M');

        $globalAccess = $authACL->canViewAllTrainingRecords();

        $instructor = $request->input('instructor', null);
        $facility = $request->input('facility', null);
        $interval = $request->input('interval', 30);
        $facilities = Facility::active()->get();

        if (!$globalAccess) {
            $facility = Auth::user()->facilityObj->id;
        }

        // START NEW CODE

        // List of training staff
        $insByRoleQuery = Role::where('role', 'INS');
        if ($facility) {
            $insByRoleQuery->where('facility', $facility);
        }
        $insByRole = $insByRoleQuery->get();

        $insByRatingQuery = User::where('flag_homecontroller', 1)
            ->where('rating', '>=', Helper::ratingIntFromShort("I1"))
            ->where('rating', '<=', Helper::ratingIntFromShort("I3"));
        if ($facility) {
            $insByRatingQuery->where('facility', $facility);
        }
        $insByRating = $insByRatingQuery->get();

        $instructors = [];
        foreach ($insByRole as $ins) {
            $instructors[$ins->cid] = $ins->user;
        }
        foreach ($insByRating as $ins) {
            $instructors[$ins->cid] = $ins;
        }

        $mtrByRoleQuery = Role::where('role', 'MTR');
        if ($facility) {
            $mtrByRoleQuery->where('facility', $facility);
        }
        $mtrByRole = $mtrByRoleQuery->get();
        $mentors = [];
        foreach ($mtrByRole as $mtr) {
            $mentors[] = $mtr->user;
        }

        $trainingStaffCount = count($instructors) + count($mentors);

        // TrainingRecord based calculations
        $recordQuery = TrainingRecord::where('session_date', '>', Carbon::now()->subDays(90));
        if ($facility) {
            $recordQuery->where('facility_id', $facility);
        }

        $allRecords = $recordQuery->get();
        $totalTime = 0;
        $timePeriods = [];
        $sessionPeriods = [];
        $trainingStaffPeriods = [];
        foreach ($instructors as $ts) {
            $trainingStaffPeriods[$ts->cid] = [
                30 => ['time' => 0, 'sessions' => 0],
                60 => ['time' => 0, 'sessions' => 0],
                90 => ['time' => 0, 'sessions' => 0],
            ];
        }
        foreach ($mentors as $ts) {
            $trainingStaffPeriods[$ts->cid] = [
                30 => ['time' => 0, 'sessions' => 0],
                60 => ['time' => 0, 'sessions' => 0],
                90 => ['time' => 0, 'sessions' => 0],
            ];
        }

        foreach ([30, 60, 90] as $period) {
            $timePeriods[$period] = 0;
            $sessionPeriods[$period] = 0;
        }

        foreach ($allRecords as $record) {
            $session_time = time_to_seconds($record->duration);
            if (Carbon::now()->subDays(30)->lessThanOrEqualTo($record->session_date)) {
                $totalTime += $session_time;
            }

            foreach ([30, 60, 90] as $period) {
                if (Carbon::now()->subDays($period)->lessThanOrEqualTo($record->session_date)) {
                    $timePeriods[$period] += $session_time;
                    $sessionPeriods[$period]++;
                    if (array_key_exists($record->instructor_id, $trainingStaffPeriods)) {
                        $trainingStaffPeriods[$record->instructor_id][$period]['time'] += $session_time;
                        $trainingStaffPeriods[$record->instructor_id][$period]['sessions']++;
                    }
                }
            }

        }
        $sumTotalTimeStr = seconds_to_string($totalTime);
        $sumTotalSessions = count($allRecords);
        $avgTime = ($trainingStaffCount > 0) ? $totalTime / $trainingStaffCount : 0;
        $sumAvgTimeStr = seconds_to_string($avgTime);
        $sumAvgSessions = ($trainingStaffCount > 0) ? $sumTotalSessions / $trainingStaffCount : 0;


        // Instructor / Mentor Activity

        $activityTableData = [];
        //
        foreach ($instructors as $ts) {
            $row = [];
            $row['name'] = $ts->fullname();
            $row['hours'] = [
                30 => seconds_to_string($trainingStaffPeriods[$ts->cid][30]['time']),
                60 => seconds_to_string($trainingStaffPeriods[$ts->cid][60]['time']),
                90 => seconds_to_string($trainingStaffPeriods[$ts->cid][90]['time']),
            ];
            $row['sessions'] = [
                30 => $trainingStaffPeriods[$ts->cid][30]['sessions'],
                60 => $trainingStaffPeriods[$ts->cid][60]['sessions'],
                90 => $trainingStaffPeriods[$ts->cid][90]['sessions'],
            ];
            $row['role'] = 'INS';
            $activityTableData[] = $row;
        }
        foreach ($mentors as $ts) {
            $row = [];
            $row['name'] = $ts->fullname();
            $row['hours'] = [
                30 => seconds_to_string($trainingStaffPeriods[$ts->cid][30]['time']),
                60 => seconds_to_string($trainingStaffPeriods[$ts->cid][60]['time']),
                90 => seconds_to_string($trainingStaffPeriods[$ts->cid][90]['time']),
            ];
            $row['sessions'] = [
                30 => $trainingStaffPeriods[$ts->cid][30]['sessions'],
                60 => $trainingStaffPeriods[$ts->cid][60]['sessions'],
                90 => $trainingStaffPeriods[$ts->cid][90]['sessions'],
            ];
            $row['role'] = 'MTR';
            $activityTableData[] = $row;
        }

        // OTSEval based calculations
        $evalQuery = OTSEval::where('exam_date', '>', Carbon::now()->subDays($interval));
        if ($facility) {
            $evalQuery = $evalQuery->where('facility_id', $facility);
        }
        $allEvals = $evalQuery->get();
        $evalPass = 0;
        $evalFail = 0;
        foreach ($allEvals as $eval) {
            if ($eval->result == 1) {
                $evalPass++;
            } else if ($eval->result == 0) {
                $evalFail++;
            }
        }
        $sumNumEvals = count($allEvals);
        $sumNumPass = $evalPass;
        $sumNumFail = $evalFail;
        $sumPassRate = $sumNumEvals ? round($sumNumPass / $sumNumEvals * 100) : 0;


        // OLD CODE BELOW

        /** Summary */

        $colors = [];

        //Hours per Month
        $hoursPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];
        $allIns = Facility::getFacTrainingStaff($facility);
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $hoursPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $hoursPerMonth = TrainingRecord::with(['instructor:cid,fname,lname'])
                ->selectRaw("SUM(TIME_TO_SEC(duration)) AS sum, instructor_id, DATE_FORMAT(session_date, '%Y-%m') AS month");
            if ($facility) {
                $hoursPerMonth = $hoursPerMonth->where('facility_id', $facility);
            }
            $hoursPerMonth = $hoursPerMonth->where('session_date', '>',
                Carbon::parse('first day of this month')->subMonths(6))
                ->whereRaw("DATE_FORMAT(session_date, '%Y-%m') = '$month'")->groupBy([
                    'month',
                    'instructor_id'
                ])->orderBy('month', 'ASC')->get();
            //dd(str_replace_array('?', $hoursPerMonth->getBindings(), $hoursPerMonth->toSql()));
            //dd($hoursPerMonth->get()->toArray());

            if ($facility) {
                foreach ($allIns as $type => $arr) {
                    foreach ($arr as $ins) {
                        if (!User::find($ins['cid'])) {
                            continue;
                        }
                        $datasets[$ins['cid']]['label'] = $ins['name'];
                        $filter = $hoursPerMonth->filter(function ($q) use ($ins) {
                            return $q->instructor_id == $ins['cid'];
                        });
                        $datasets[$ins['cid']]['data'][] = $filter->count() ? floor($filter->first()->sum / 3600) : 0;
                    }
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = floor($hoursPerMonth->filter(function ($q) {
                        return !is_null(User::find($q->instructor_id));
                    })->pluck('sum')->sum() / 3600);
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $hoursPerMonthData['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                'backgroundColor' => $colors[$k]
            ];
        }

        //Time per Instructor
        $timePerInstructorData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        if ($facility) {
            $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($interval));
            if ($facility) {
                $records = $records->where('facility_id', $facility);
            }
            $timePerInstructorData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
            $timePerInstructor = $records->with(['instructor:cid,fname,lname'])
                ->selectRaw('SUM(TIME_TO_SEC(duration)) AS total, instructor_id')
                ->groupBy(['instructor_id']);
            foreach ($timePerInstructor->get() as $time) {
                if (!User::find($time->instructor_id)) {
                    continue;
                }
                $timePerInstructorData['labels'][] = $time->instructor->fullname();
                $timePerInstructorData['datasets'][0]['data'][] = floor($time->total / 3600);
                $timePerInstructorData['datasets'][0]['backgroundColor'][] =
                    $colors[$time->instructor->cid] ?? Factory::create()->hexColor;
            }
        }

        //Table Data
        $i = 0;
        //dd($insWithSparklines);

        /** OTS Evaluations */

        //Evals Conducted per Month
        $colors = [];
        $evalsPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];

        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $evalsPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $evalsPerMonth =
                OTSEval::with('form:id,name')->selectRaw("form_id, DATE_FORMAT(exam_date, '%Y-%m') AS month");
            if ($facility) {
                $evalsPerMonth->where('facility_id', $facility);
            }
            if ($instructor) {
                $evalsPerMonth->where('instructor_id', $instructor);
            }
            $evalsPerMonth = $evalsPerMonth->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->get();
            $k = 0;
            foreach (OTSEvalForm::active()->noStatements()->get() as $form) {
                $datasets[$k]['data'][] = $evalsPerMonth->filter(function ($e) use ($form) {
                    return $e->form->id == $form->id;
                })->count();
                $datasets[$k++]['label'] = $form->name;
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $evalsPerMonthData['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                'backgroundColor' => $colors[$k]
            ];
        }

        //Completed Evaluations per Form
        $evals = OTSEval::where('exam_date', '>=', Carbon::now()->subDays($interval));
        if ($facility) {
            $evals->where('facility_id', $facility);
        }
        $evalsPerFormData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        $evalsPerForm = $evals->with(['form:id,name'])->selectRaw('COUNT(*) AS total, form_id')
            ->groupBy([DB::raw('form_id')]);
        foreach ($evalsPerForm->get() as $eval) {
            if (!$eval->form_id) {
                continue;
            }
            $evalsPerFormData['labels'][] = $eval->form->name;
            $evalsPerFormData['datasets'][0]['data'][] = $eval->total;
            $evalsPerFormData['datasets'][0]['backgroundColor'][] =
                $colors[$eval->form_id] ?? Factory::create()->hexColor;
        }

        //Table Data
        $evalFormsTable = [];
        $i = 0;
        $evalForms = OTSEvalForm::active()->noStatements()->orderBy('rating_id')->get();
        foreach ($evalForms as $form) {
            $evalFormsTable[$i]['name'] = $form->name;
            $evalFormsTable[$i]['id'] = $form->id;
            $evalFormsTable[$i]['sparkline'] = $form->getStatSparkline($facility);

            for ($k = 30; $k <= 90; $k += 30) {
                $completed = $form->evaluations()->where('exam_date', '>=', Carbon::now()->subDays($k));
                if ($facility) {
                    $completed->where('facility_id', $facility);
                }
                $numConducted = $completed->count();
                if (!$numConducted) {
                    $passRate = '<em>N/A</em>';
                    $numPass = $numFail = 0;
                } else {
                    $numPass = $completed->where('result', 1)->count();
                    $numFail = $numConducted - $numPass;
                    $passRate = floor($numPass / $numConducted * 100);
                }
                $evalFormsTable[$i]['passRate'][$k] = $passRate;
                $evalFormsTable[$i]['numPass'][$k] = $numPass;
                $evalFormsTable[$i]['numFail'][$k] = $numFail;
                $evalFormsTable[$i]['numConducted'][$k] = $numConducted;
            }
            $i++;
        }
        //Evals Conducted per Month - INS
        $evalsPerMonthDataIns = ['labels' => [], 'datasets' => []];
        $evalsPerFormDataIns = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        if ($facility) {
            $colors = [];
            $datasets = [];
            for ($i = 6; $i >= 0; $i--) {
                $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
                $evalsPerMonthDataIns['labels'][] =
                    Carbon::parse('first day of this month')->subMonths($i)->format('F');

                $evalsPerMonth = OTSEval::with('instructor:cid,fname,lname')
                    ->selectRaw("instructor_id, DATE_FORMAT(exam_date, '%Y-%m') AS month");

                $evalsPerMonth->where('facility_id', $facility);
                $evalsPerMonth =
                    $evalsPerMonth->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->orderBy('month',
                        'ASC')->get();
                // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
                //dd($hoursPerMonth->get()->toArray());
                foreach ($allIns['ins'] as $ins) {
                    if (!User::find($ins['cid'])) {
                        continue;
                    }
                    $datasets[$ins['cid']]['label'] = $ins['name'];
                    $filter = $evalsPerMonth->filter(function ($q) use ($ins) {
                        return $q->instructor_id == $ins['cid'];
                    });
                    $datasets[$ins['cid']]['data'][] = $filter->count();
                }
            }
            foreach ($datasets as $k => $v) {
                $colors[$k] = Factory::create()->hexColor;
                $evalsPerMonthDataIns['datasets'][] = [
                    'label' => $v['label'],
                    'data' => $v['data'],
                    'backgroundColor' => $colors[$k]
                ];
            }

            //Completed Evaluations per Form - INS
            $evals = OTSEval::where('exam_date', '>=', Carbon::now()->subDays($interval));
            if ($facility) {
                $evals->where('facility_id', $facility);
            }
            $evalsPerForm = $evals->with(['instructor:cid,fname,lname'])->selectRaw('COUNT(*) AS total, instructor_id')
                ->groupBy(['instructor_id']);
            foreach ($evalsPerForm->get() as $eval) {
                if (!User::find($eval->instructor_id)) {
                    continue;
                }
                $evalsPerFormDataIns['labels'][] = $eval->instructor->fullname();
                $evalsPerFormDataIns['datasets'][0]['data'][] = $eval->total;
                $evalsPerFormDataIns['datasets'][0]['backgroundColor'][] =
                    $colors[$eval->instructor->cid] ?? Factory::create()->hexColor;
            }
        }

        /** Training Records */
        $colors = [];
        $recordsPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];
        $recordsPerMonth =
            TrainingRecord::selectRaw("COUNT(*) AS total, position, DATE_FORMAT(session_date, '%Y-%m') AS month");
        if ($facility) {
            $recordsPerMonth->where('facility_id', $facility);
        }
        $recordsPerMonth->whereRaw("DATE_FORMAT(session_date, '%Y-%m') != DATE_FORMAT(NOW(), '%Y-%m')")->groupBy([
            'month',
            'position'
        ])->orderBy('month', 'ASC');
        // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
        //dd($hoursPerMonth->get()->toArray());
        $recordsPerMonth = TrainingRecord::selectRaw("position, DATE_FORMAT(session_date, '%Y-%m') AS month");
        if ($facility) {
            $recordsPerMonth->where('facility_id', $facility);
        }
        $recordsPerMonth->whereRaw("session_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)")->orderBy('month', 'ASC');
        $allPos = $recordsPerMonth->get()->pluck('position')->unique()->all();
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $recordsPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $recordsPerMonth = TrainingRecord::selectRaw("position, DATE_FORMAT(session_date, '%Y-%m') AS month");
            if ($facility) {
                $recordsPerMonth->where('facility_id', $facility);
            }
            $recordsPerMonth =
                $recordsPerMonth->whereRaw("DATE_FORMAT(session_date, '%Y-%m') = '$month'")->orderBy('month',
                    'ASC');
            if ($facility) {
                foreach ($allPos as $pos) {
                    $datasets[$pos]['data'][] = $recordsPerMonth->get()->filter(function ($q) use ($pos) {
                        return $q->position === $pos;
                    })->count();
                    $datasets[$pos]['label'] = $pos;
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = $recordsPerMonth->count();
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $recordsPerMonthData['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                'borderColor' => $colors[$k]
            ];
        }

        //Records per Type
        $records = TrainingRecord::where('session_date', '>=', Carbon::now()->subDays($interval));
        if ($facility) {
            $records->where('facility_id', $facility);
        }
        $recordsPerTypeData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        $recordsPerType = $records->selectRaw('COUNT(*) AS total, position')
            ->groupBy(['position']);
        foreach ($recordsPerType->get() as $record) {
            if (!$record->total) {
                continue;
            }
            $recordsPerTypeData['labels'][] = $record->position;
            $recordsPerTypeData['datasets'][0]['data'][] = $record->total;
            $recordsPerTypeData['datasets'][0]['backgroundColor'][] =
                $colors[$record->position] ?? Factory::create()->hexColor;
        }

        //Table Data
        $trainingRecords = TrainingRecord::with(['instructor:cid,fname,lname', 'student:cid,fname,lname'])
            ->where('session_date', '>=', Carbon::now()->subDays($interval));
        if ($facility) {
            $trainingRecords->where('facility_id', $facility);
        }
        $trainingRecords = $trainingRecords->get();

        return view('mgt.training.stats',
            compact('instructor', 'facility', 'sumTotalSessions', 'sumTotalTimeStr',
                'sumAvgTimeStr', 'sumAvgSessions', 'sumNumPass', 'sumNumFail', 'sumPassRate', 'hoursPerMonthData',
                'timePerInstructorData', 'evalsPerMonthData', 'evalsPerFormData', 'evalsPerFormDataIns',
                'evalsPerMonthDataIns', 'evalFormsTable', 'recordsPerTypeData', 'recordsPerMonthData',
                'trainingRecords', 'facilities', 'activityTableData'));
    }

    public
    function viewEvals(
        Request $request
    )
    {
        if (!AuthHelper::authACL()->canViewTrainingRecords()) {
            abort(403);
        }

        /** Training Records */
        $trainingfac = $request->input('fac', null);
        $facilities = Facility::active()->get();

        if (!$trainingfac) {
            if (AuthHelper::authACL()->isVATUSAStaff()) {
                $trainingfac = "";
                $trainingfacname = "";
            } else {
                $trainingfac = Auth::user()->facility;
                $trainingfacname = Auth::user()->facility()->name;
            }
        } else {
            if (Facility::find($trainingfac)) {
                $trainingfacname = Helper::facShtLng($trainingfac);
            } else {
                $trainingfac = Auth::user()->facility;
                $trainingfacname = Auth::user()->facility()->name;
            }
        }
        $evals = $trainingfac ? Facility::find($trainingfac)->evaluations()->where('facility_id',
            $trainingfac)->get() : [];

        return view('mgt.training.evals',
            compact('evals', 'trainingfac', 'trainingfacname', 'facilities'));
    }

    public
    function viewOTSEvalStatistics(
        Request $request,
        int     $form
    )
    {
        $form = OTSEvalForm::withAll()->find($form);
        if (!$form) {
            abort(404, "The Rating Exam evaluation form is invalid.");
        }

        $instructor = $request->input('instructor', null);
        $facility = $request->input('facility', null);
        $facilities = Facility::active()->get();
        $interval = intval($request->input('interval', 15)); //Last num of tests
        if (!$interval) {
            abort(400);
        }
        $authACL = AuthHelper::authACL();
        if (!$authACL->canViewTrainingRecords()) {
            abort(403);
        }

        $hasGlobalAccess = $authACL->isVATUSAStaff();
        if (!$hasGlobalAccess) {
            $facility = Auth::user()->facility();
        } else if ($facility) {
            $facility = Facility::find($facility);
            if (!$facility) {
                abort(404, "Facility not found.");
            }
        }

        //Chart 1: Stacked Line, Num Pass and Fails per Month
        $colors = ['rgb(255, 99, 132)', 'rgb(75, 192, 192)'];
        $numPassFailsData = ['labels' => [], 'datasets' => []];
        $datasets = [];

        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $numPassFailsData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');
            $numPassFails = OTSEval::selectRaw("result, DATE_FORMAT(exam_date, '%Y-%m') as month")
                ->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'");
            if ($facility) {
                $numPassFails->where('facility_id', $facility->id);
            }
            if ($instructor) {
                $numPassFails->where('instructor_id', $instructor);
            }
            $numPassFails = $numPassFails->where('form_id', $form->id)->orderBy('month',
                'ASC')->get();
            //dd(str_replace_array('?', $numPassFails->getBindings(), $numPassFails->toSql()));
            //dd($hoursPerMonth->get()->toArray());
            $totalPass = $numPassFails->filter(function ($q) {
                return $q->result;
            })->count();
            $totalFail = $numPassFails->filter(function ($q) {
                return !$q->result;
            })->count();
            $datasets[0]['data'][] = $totalFail;
            $datasets[0]['label'] = 'Fail';
            $datasets[1]['data'][] = $totalPass;
            $datasets[1]['label'] = 'Pass';
        }
        foreach ($datasets as $k => $v) {
            $numPassFailsData['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                'borderColor' => $colors[$k]
            ];
        }

        //Chart 2: Stacked Bar, Number of Evaluations by INS per Month
        $evalsPerMonthDataIns = ['labels' => [], 'datasets' => []];
        $allIns = [];
        $datasets = [];
        $allIns = $facility ? Facility::getFacTrainingStaff($facility->id)['ins'] : [];
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $evalsPerMonthDataIns['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $evalsPerMonth = OTSEval::selectRaw("DATE_FORMAT(exam_date, '%Y-%m') AS month, instructor_id");
            if ($facility) {
                $evalsPerMonth->where('facility_id', $facility->id);
            }
            $evalsPerMonth = $evalsPerMonth->where('form_id',
                $form->id)->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->orderBy('month', 'ASC')->get();
            if ($facility && !$instructor) {
                foreach ($allIns as $ins) {
                    // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
                    //dd($hoursPerMonth->get()->toArray());
                    if (!User::find($ins['cid'])) {
                        continue;
                    }

                    $datasets[$ins['cid']]['label'] = $ins['name'];
                    $datasets[$ins['cid']]['data'][] = $evalsPerMonth->filter(function ($e) use ($ins) {
                        return $e->instructor_id == $ins['cid'];
                    })->count();
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = $instructor ? $evalsPerMonth->filter(function ($e) use ($instructor) {
                    return User::find($e->instructor_id) && $e->instructor_id == $instructor;
                })->count() : $evalsPerMonth->count();
            }
        }
        foreach ($datasets as $k => $v) {
            $evalsPerMonthDataIns['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                $facility && !$instructor ? 'borderColor' : 'backgroundColor' => Factory::create()->hexColor
            ];
        }
        //Table: INS Name (SL: Pass/Fail last 15 num of tests), Num Passes (30/60/90), Num Fails (30/60/90)
        $tableData = [];
        if ($facility) {
            for ($i = 0; $i < count($allIns); $i++) {
                $tableData[$i]['name'] = $allIns[$i]['name'];
                $tableData[$i]['sparkline'] = "";
                $evals = OTSEval::where('instructor_id', $allIns[$i]['cid'])
                    ->where('form_id', $form->id)->where('facility_id', $facility->id)
                    ->orderBy('exam_date', 'ASC')->limit(10)->pluck('result')->all();
                for ($k = 0; $k < count($evals); $k++) {
                    $tableData[$i]['sparkline'] .= ($evals[$k] == 1 ? 1 : -1) . ($k + 1 == count($evals) ? '' : ",");
                }

                for ($k = 30; $k <= 90; $k += 30) {
                    $evals = OTSEval::where([
                        'instructor_id' => $allIns[$i]['cid'],
                        'form_id' => $form->id,
                        'facility_id' => $facility->id,
                        ['exam_date', '>=', Carbon::now()->subDays($k)]
                    ])->get();
                    //if($allIns[$i]['cid'] == 1275302) dd(str_replace_array('?', $evals->getBindings(), $evals->toSql()));
                    //  else $evals = $evals->get();
                    $tableData[$i]['numPasses'][$k] = $evals->filter(function ($e) {
                        return $e->result;
                    })->count();
                    $tableData[$i]['numFails'][$k] = $evals->filter(function ($e) {
                        return !$e->result;
                    })->count();
                }
            }
        }

        return view('mgt.training.otsEvalStats',
            compact('form', 'instructor', 'facilities', 'interval', 'facility',
                'numPassFailsData', 'evalsPerMonthDataIns', 'allIns', 'tableData', 'hasGlobalAccess'));
    }
}
