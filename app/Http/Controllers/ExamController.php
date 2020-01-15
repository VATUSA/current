<?php

namespace App\Http\Controllers;

use App\ExamResults;
use App\ExamResultsData;
use Illuminate\Http\Request;
use App\Classes\RoleHelper;
use App\Classes\EmailHelper;
use App\Classes\ExamHelper;
use App\Classes\Helper;
use App\Actions;
use App\Exam;
use App\ExamQuestions;
use App\ExamAssignment;
use App\ExamReassignment;
use App\Facility;
use App\User;
use Auth;
use App\TrainingBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ExamController extends Controller
{
    public function getResult($id)
    {
        if (!Auth::check()) {
            abort(401);
        }

        $result = ExamResults::find($id);
        if (!$result) {
            abort(404);
        }

        if (
            !(RoleHelper::isInstructor() ||
                RoleHelper::isFacilitySeniorStaff() ||
                RoleHelper::isVATUSAStaff())
            && $result->cid != Auth::user()->cid
        ) {
            abort(401);
        }


        if (!$result) {
            abort(404);
        }

        $user = User::where('cid', $result->cid)->first();
        $resultdata = ExamResultsData::where('result_id', $id)->get();

        return view('exams.result', ['user' => $user, 'result' => $result, 'resultdata' => $resultdata]);
    }

    public function getIndex()
    {
        /*if (!RoleHelper::isInstructor() && !RoleHelper::isFacilitySeniorStaff())
            abort(401);*/

        if (!Auth::check()) {
            return redirect('/')->with("error", "You must be logged in for that.");
        }

        return View('exams.index');
    }

    public function getDownload(Request $request, $id)
    {
        if (!Auth::check()) {
            abort(401);
        }

        $exam = Exam::find($id);
        if (!$this->accessCheckExam(Auth::user()->cid, $exam->facility_id)) {
            abort(401);
        }

        $questions = $exam->questions()->get();
        $csv = "id,exam_id,question,type,answer,alt1,alt2,alt3,\n";
        foreach ($questions as $q) {

            $data = [
                'id'       => $q->id,
                'exam_id'  => $q->exam_id,
                'question' => '"' . preg_replace("/[\n\r]/", "", str_replace('"', '""', $q->question)) . '"',
                'type'     => (($q->type) ? "TF" : "MC"),
                'answer'   => '"' . $q->answer . '"',
                'alt1'     => '"' . $q->alt1 . '"',
                'alt2'     => '"' . $q->alt2 . '"',
                'alt3'     => '"' . $q->alt3 . '"',
            ];
            $csv .= implode(",", $data) . ",\n";
        }

        return (new Response($csv, 200))->header('Content-Type', 'text/csv')->header('Content-Disposition',
            'attachment; filename="' . $id . '.csv"');
    }

    /** Take Exam area **/

    public function getTakeExam(Request $request, $id)
    {
        if (!Auth::check()) {
            abort(401);
        }

        if ($id == 0) {
            $exams = ExamAssignment::where('cid', Auth::user()->cid)->where('expire_date', '>',
                DB::raw('NOW()'))->get();

            return View('exams.viewassignments', ['exams' => $exams]);
        }

        // Check if assigned. Make sure expire date is after now, in case our auto-expire hasn't caught it yet.
        $assign = ExamAssignment::where('cid', Auth::user()->cid)->where('exam_id', $id)->where('expire_date', '>',
            \DB::raw('NOW()'))->first();

        // No assignment, redirect to exam assignments with error.
        if ($assign == null) {
            return redirect('/exam/0')->with('error', 'Exam is not assigned.');
        }

        $exam = Exam::find($id);

        if (!ExamHelper::examCBTComplete($exam)) {
            return redirect("/exam")->with("error", "You have not completed the CBT Requirements");
        }

        if ($exam->number > 0) {
            $questions = $exam->questions()->orderBy(\DB::raw('RAND()'))->take($exam->number)->get();
        } else {
            $questions = $exam->questions()->orderBy(\DB::raw('RAND()'))->get();
        }
        $json = [
            'id'           => $exam->id,
            'name'         => $exam->name,
            'facility'     => $exam->facility_id,
            'facilityName' => $exam->facility()->first()->name
        ];
        $x = 0;
        if ($request->session()->has('examdata') && $request->session()->get('examid') == $exam->id) {
            $json = $request->session()->get('examdata');
        } else {
            foreach ($questions as $question) {
                $questiontemp = [
                    'id'           => $question->id,
                    'question'     => preg_replace("/\r?\n/", '<br>', $question->question),
                    'illustration' => $question->illustration,
                    'type'         => $question->type
                ];
                if ($question->type == 0) {
                    $questiontemp['one'] = preg_replace("/\r?\n/", '<br>', $question->answer);
                    $questiontemp['two'] = preg_replace("/\r?\n/", '<br>', $question->alt1);
                    $questiontemp['three'] = preg_replace("/\r?\n/", '<br>', $question->alt2);
                    $questiontemp['four'] = preg_replace("/\r?\n/", '<br>', $question->alt3);
                } else {
                    $questiontemp['one'] = $question->answer;
                }

                $json['questions'][] = $questiontemp;
                $x++;
            }
            $json['count'] = $x;
            $json = json_encode($json, JSON_HEX_APOS);
            $request->session()->put('examdata', $json);
            $request->session()->put('examid', $exam->id);
        }
        $json = str_replace("\\", "\\\\", $json);

        return View('exams.take', ['json' => $json]);
    }

    public function putTakeExam(Request $request, $id)
    {
        if (!Auth::check() || !$request->ajax() || $id == 0) {
            abort(401, "You are not logged in or your session has timed out");
        }

        if (!$request->session()->has('examdata')) {
            abort(500, "Exam Data not set.  Unable to grade.");
        }

        // Check if assigned. Make sure expire date is after now, in case our auto-expire hasn't caught it yet.
        $assign = ExamAssignment::where('cid', Auth::user()->cid)->where('exam_id', $id)->where('expire_date', '>=',
            \DB::raw('NOW()'))->first();

        if ($assign == null) {
            abort(401, "There is no exam assignment");
        }

        //var_dump($assign); exit;

        parse_str(file_get_contents("php://input"), $vars);
        $answers = json_decode($vars['answer']);
        $exam = Exam::find($id);
        if ($exam == null) {
            abort(401);
        }

        $correct = 0;
        $possible = 0;

        $result = new ExamResults();
        $result->exam_id = $exam->id;
        $result->exam_name = $exam->name;
        $result->cid = Auth::user()->cid;
        $result->date = \Carbon\Carbon::now();
        $result->save();

        $examdata = json_decode($request->session()->get('examdata'));

        foreach ($examdata->questions as $question) {
            $possible++;

            $resultdata = new ExamResultsData();
            $resultdata->result_id = $result->id;
            $resultdata->question = $question->question;
            $resultdata->correct = $question->one;
            $resultdata->is_correct = 0;
            foreach ($answers as $answer) {
                if ($answer->id == $question->id) {
                    if ($question->type == 1) {
                        $resultdata->selected = $answer->answer;
                        if ($answer->answer == $question->one) {
                            $correct++;
                            $resultdata->is_correct = 1;
                        }
                    } elseif ($question->type == 0) {
                        if ($answer->answer == "1") {
                            $correct++;
                            $resultdata->is_correct = 1;
                            $resultdata->selected = $question->one;
                        } else {
                            if ($answer->answer == "2") {
                                $resultdata->selected = $question->two;
                            }
                            if ($answer->answer == "3") {
                                $resultdata->selected = $question->three;
                            }
                            if ($answer->answer == "4") {
                                $resultdata->selected = $question->four;
                            }
                        }
                    }
                }
            }
            $resultdata->save();
        }

        $score = round(($correct / $possible) * 100);
        $result->score = $score;
        $result->passed = ($score >= $exam->passing_score) ? 1 : 0;
        $result->save();
        $to[] = Auth::user()->email;
        $to[] = Helper::emailFromCID($assign->instructor_id);
        if ($exam->facility_id != "ZAE") {
            $to[] = $exam->facility_id . "-TA@vatusa.net";
        }

        $request->session()->forget('examdata');
        $request->session()->forget('examid');

        $data = [
            'exam_name'       => "(" . $exam->facility_id . ") " . $exam->name,
            'instructor_name' => Helper::nameFromCID($exam->instructor_id),
            'correct'         => $correct,
            'possible'        => $possible,
            'score'           => $score,
            'student_name'    => Helper::nameFromCID(Auth::user()->cid),
            'reassign'        => $exam->retake_period
        ];

        $log = new Actions();
        $log->to = Auth::user()->cid;
        $log->log = "Exam " . "(" . $exam->facility_id . ") " . $exam->name . " completed.  Score: $correct/$possible ($score%). ";
        $log->log .= (($score >= $exam->passing_score) ? "Passed." : "Not Passed.");
        $log->save();

        if ($score >= $exam->passing_score) {
            $assign->delete();
            $fac = $exam->facility_id;
            if ($fac == "ZAE") {
                $fac = Auth::user()->facility;
            }
            EmailHelper::sendEmailFacilityTemplate($to, "Exam Passed", $fac, "exampassed", $data);
            if ($exam->id == config("exams.BASIC")) {
                Auth::user()->flag_needbasic = 0;
                Auth::user()->save();
            }

            return view("exams.partialpassed");
        } else {
            if ($exam->retake_period > 0) {
                $reassign = new ExamReassignment();
                $reassign->cid = $assign->cid;
                $reassign->instructor_id = $assign->instructor_id;
                $reassign->exam_id = $assign->exam_id;
                $reassign->reassign_date = \Carbon\Carbon::now()->addDays($exam->retake_period);
                $reassign->save();
            }
            $assign->delete();
            $fac = $exam->facility_id;
            if ($fac == "ZAE") {
                $fac = Auth::user()->facility;
            }
            EmailHelper::sendEmailFacilityTemplate($to, "Exam Not Passed", $fac, "examfailed", $data);

            return view("exams.partialfailed", ["reassign" => $exam->retake_period]);
        }
    }

    /** Assignment Functions **/

    public function getAssign()
    {
        $this->canAssignExam();

        if (RoleHelper::isVATUSAStaff()) {
            $exams = Exam::where('is_active', 1)->orderBy('name')->get();
        } else {
            $exams = Exam::where('is_active', 1)->where(function ($query) {
                $query->where('facility_id', 'ZAE')
                    ->orWhere('facility_id', Auth::user()->facility);
            })->orderBy('name')->get();
        }

        $examArr = array();
        foreach ($exams as $exam) {
            $examArr[$exam->facility->name][] = array(
                'id'   => $exam->id,
                'name' => $exam->name
            );
        }
        ksort($examArr);

        return View('exams.assign', ['exams' => $examArr, 'expireoptions' => ExamHelper::expireOptions()]);
    }

    public function postAssign()
    {
        $exam = Exam::find($_POST['exam']);
        if ($exam == null) {
            abort(404);
        }
        $this->canAssignExam(null, $exam);

        if (User::where('cid', $_POST['cid'])->count() == 0) {
            return redirect('/exam/assign')->with("error", "User not found.");
        }

        if (ExamHelper::isAssigned($_POST['cid'], $_POST['exam'])) {
            $error = "Exam already assigned.";
        } else {
            ExamHelper::assign($_POST['cid'], $_POST['exam'], null, $_POST['expire']);
            $success = "Exam assigned.";
        }

        if (RoleHelper::isVATUSAStaff()) {
            $exams = Exam::orderBy('facility_id')->orderBy('name')->get();
        } else {
            $exams = Exam::where('facility_id', Auth::user()->facility)->orWhere('facility_id',
                'ZAE')->orderBy('name')->get();
        }

        $return = ['exams' => $exams, 'expireoptions' => ExamHelper::expireOptions()];
        if (isset($error)) {
            $return['error'] = $error;
        }
        if (isset($success)) {
            $return['success'] = $success;
        }

        return View('exams.assign', $return);
    }

    /** Assignment Handlers **/
    public
    function getAssignments(
        $fac = null
    ) {
        $this->canAssignExam();

        if ($fac == null && !RoleHelper::isVATUSAStaff() && !RoleHelper::isAcademyStaff()) {
            $fac = Auth::user()->facility;
        }

        if ($fac == null) {
            $facilities = Facility::where("active", 1)->orderBy('id')->get();

            return View('exams.assignmentsselect', ['facilities' => $facilities]);
        }

        $exams = Exam::where('facility_id', $fac)->where('is_active', 1)->orderBy('name')->get();

        return View('exams.assignments', ['fac' => $fac, 'exams' => $exams]);
    }

    public
    function deleteAssignment(
        $id
    ) {
        $assignment = ExamAssignment::find($id);
        if ($assignment == null) {
            abort(500);
        }

        $exam = Exam::find($assignment->exam_id);

        $this->canAssignExam(null, $exam);

        $log = new Actions();
        $log->to = $assignment->cid;
        $log->log = "Exam " . $exam->name . " unassigned by " . Auth::user()->fullname() . ".";
        $log->save();

        $assignment->delete();
    }

    public
    function deleteReassignment(
        $id
    ) {
        $assignment = ExamReassignment::find($id);
        if ($assignment == null) {
            abort(500);
        }

        $exam = Exam::find($assignment->exam_id);

        $this->canAssignExam(null, $exam);

        $log = new Actions();
        $log->to = $assignment->cid;
        $log->log = "Exam " . $exam->name . " unassigned by " . Auth::user()->fullname() . ".";
        $log->save();

        $assignment->delete();
    }

    /** Editor Functions **/

    public
    function getEdit()
    {
        $this->accessCheckExam();

        if (RoleHelper::isVATUSAStaff()) // Run model for all exams
        {
            $exams = Exam::orderBy('facility_id')->orderBy('name')->get();
        } else {
            $exams = Exam::where('facility_id', Auth::user()->facility)->orderBy('name')->get();
        }

        return View('exams.edit')->with('exams', $exams);
    }

    public function getDeleteExam($id)
    {
        $exam = Exam::find($id);
        $this->accessCheckExam(Auth::user()->cid, $exam->facility_id);

        $fac = $exam->facility_id;
        $name = $exam->name;

        DB::raw('DELETE FROM exam_questions WHERE exam_id=' . $exam->id);
        DB::raw('DELETE FROM exam_assignments WHERE exam_id=' . $exam->id);
        DB::raw('DELETE FROM exam_reassignments WHERE exam_id=' . $exam->id);
        $exam->delete();

        return redirect('/exam/edit')->with("success", "Exam '$name' successfully deleted");
    }

    public
    function getEditQuestion(
        $examid,
        $questionid
    ) {
        $exam = Exam::find($examid);
        $this->accessCheckExam(null, $exam->facility_id);
        if ($questionid > 0) {
            $question = ExamQuestions::find($questionid);
            if ($question->exam_id != $examid) {
                abort(401);
            }
        }

        if ($questionid == 0) {
            $question = new ExamQuestions();
            $question->exam_id = $exam->id;
            $question->type = 1;
            $question->question = "New Question";
            $question->save();
        }

        return View('exams.editquestion', ['exam' => $exam, 'question' => $question]);
    }

    public
    function postEditQuestion(
        $examid,
        $questionid
    ) {
        if ($questionid == 0) {
            abort(401);
        }

        $exam = Exam::find($examid);
        $this->accessCheckExam(null, $exam->facility_id);
        $question = ExamQuestions::find($questionid);
        if ($question->exam_id != $examid) {
            abort(500);
        }

        $question->question = $_POST['question'];
        $question->type = $_POST['qtype'];
        if ($question->type == "1") {
            $question->answer = $_POST['tfanswer'];
            $question->alt1 = "";
            $question->alt2 = "";
            $question->alt3 = "";
        } else {
            $question->answer = $_POST['correct'];
            $question->alt1 = $_POST['distractor1'];
            $question->alt2 = $_POST['distractor2'];
            $question->alt3 = $_POST['distractor3'];
        }
        $question->notes = $_POST['notes'];
        $question->save();

        return View('exams.editquestion', ['exam' => $exam, 'question' => $question, 'success' => 1]);
    }

    public
    function editExam(
        $id = null
    ) {
        if ($id == null && isset($_POST['exam'])) {
            $id = $_POST['exam'];
        } elseif ($id) {
            ;
        }   // Don't need any checks, routing did it for us.
        else {
            abort(500);
        }

        $exam = Exam::find($id);
        if ($exam == null) {
            abort(500);
        }

        // Have to make sure they are senior staff role'd for the facility of the exam... we don't care about
        // the home facility.
        $this->accessCheckExam(Auth::user()->cid, $exam->facility_id);

        $questions = $exam->questions()->get();
        $blocks = TrainingBlock::where('facility', $exam->facility_id)->orderBy("order")->get();

        return View('exams.editquestions',
            ['exam' => $exam, 'questions' => $questions, 'blocks' => $blocks, 'retakes' => ExamHelper::validRetakes()]);
    }

    public
    function postEditExam(
        $id
    ) {
        $exam = Exam::find($id);
        $this->accessCheckExam(null, $exam->facility_id);

        if (isset($_POST['cbt'])) {
            $exam->cbt_required = $_POST['cbt'];
        }
        if (isset($_POST['retake'])) {
            $exam->retake_period = $_POST['retake'];
        }
        if (isset($_POST['number'])) {
            $exam->number = $_POST['number'];
        }
        if (isset($_POST['passing'])) {
            $exam->passing_score = $_POST['passing'];
        }
        if (isset($_POST['name'])) {
            $exam->name = $_POST['name'];
        }
        if (isset($_POST['active'])) {
            $exam->is_active = $_POST['active'];
        }
        if (isset($_POST['visibility'])) {
            $exam->answer_visibility = $_POST['visibility'];
        }

        $exam->save();
    }

    public
    function getCreate()
    {
        $exam = new Exam();
        $exam->facility_id = (Auth::user()->facility == "ZHQ") ? "ZAE" : Auth::user()->facility;
        $exam->is_active = 0;
        $exam->number = 0;
        $exam->cbt_required = null;
        $exam->retake_period = 7;
        $exam->passing_score = 70;
        $exam->save();

        return redirect('/exam/edit/' . $exam->id);
    }

    public
    function deleteQuestion(
        $examid,
        $qid
    ) {
        $exam = Exam::find($examid);
        if ($exam == null) {
            abort(401);
        }

        $question = ExamQuestions::find($qid);
        $this->accessCheckExam(null, $exam->facility_id);

        if ($question == null) {
            abort(401);
        }
        $question->delete();
        echo "1";
    }

    /** Security Checks **/

    private
    function canAssignExam(
        $cid = null,
        Exam $exam = null
    ) {
        if (!Auth::check()) {
            abort(401);
        }

        if ($cid == null) {
            $cid = Auth::user()->cid;
        }

        if ($exam == null) {
            if (RoleHelper::isInstructor($cid) || RoleHelper::isFacilitySeniorStaff()) {
                return true;
            } else {
                Log::warning(Auth::user()->cid . " attempted to assign exam, not instructor");
                abort(404);
            }
        }

        if (($exam->facility_id == "ZAE" && RoleHelper::isInstructor($cid, Auth::user()->facility)) ||
            RoleHelper::isInstructor($cid, $exam->facility_id) ||
            RoleHelper::isFacilitySeniorStaff($cid, $exam->facility_id)
        ) {
            return true;
        }

        Log::warning(Auth::user()->cid . " attempted to assign exam " . $exam->id . " (401)");
        abort(401);
    }

    private
    function accessCheckExam(
        $cid = null,
        $fac = null
    ) {
        if (!Auth::check()) {
            abort(401);
        }
        if ($cid == null) {
            $cid = Auth::user()->cid;
        }
        if ($fac == null) {
            $fac = Auth::user()->facility;
        }

        if (RoleHelper::isVATUSAStaff($cid)) {
            return true;
        }
        if (RoleHelper::isFacilitySeniorStaff($cid, $fac)) {
            return true;
        }

        abort(401);
    }
}
