<?php
namespace App\Classes;

use App\Models\ExamAssignment;
use App\Models\ExamQuestions;
use App\Models\Exam;
use App\Models\Actions;
use App\Models\TrainingBlock;
use DB;
use DateTime;
use Auth;

define("BASIC_EXAM", 7);

class ExamHelper
{
    /**
     * Check if exam is assigned or scheduled for reassignment
     *
     * @param $cid
     * @param $exam
     * @param bool|true $incl_reassign
     * @return bool
     */
    public static function isAssigned($cid, $exam, $incl_reassign = true)
    {
        $as = $ras = 0;

        $as = DB::table("exam_assignments")->where("exam_id", $exam)->where("cid", $cid)->count();
        if ($incl_reassign)
            $ras = DB::table("exam_reassignments")->where("exam_id", $exam)->where("cid", $cid)->count();

        return (($as >= 1) || ($ras >= 1)) ? true : false;
    }

    /**
     * Assign exam
     *
     * @param $cid
     * @param $exam
     * @param int $expire_period
     */
    public static function assign($cid, $exam, $instructor = null, $expire_period = 7, $force = false)
    {
        if (static::isAssigned($cid, $exam) && !$force)
            return;

        if ($instructor == null) {
            $instructor = Auth::user()->cid;
        }

        if (!in_array($expire_period, ExamHelper::expireOptions())) $expire_period = 7;

        $date = new DateTime();
        $date->modify("+$expire_period day");
        $end_date = $date->format("m/d/Y");
        $end_time = $date->format("h:ia");

        DB::table('exam_assignments')->insert(
            ['cid' => $cid, 'exam_id' => $exam, 'instructor_id' => $instructor, 'assigned_date' => DB::raw('NOW()'), 'expire_date' => DB::raw("DATE_ADD(NOW(), INTERVAL $expire_period DAY)")]
        );


        $exam = Exam::find($exam);
        $fac = $exam->facility_id;

        $cbt_required = 0;
        $cbt_facility = $cbt_block = "";
        if ($exam->cbt_required) {
            $cbt_required = 1;
            $cbt = TrainingBlock::find($exam->cbt_required);
            $cbt_facility = $cbt->facility;
            $cbt_block = $cbt->name;
        }

        $data = [
            'exam_name' => "(" . $exam->facility_id . ") " . $exam->name,
            'instructor_name' => Helper::nameFromCID($instructor),
            'end_date' => $end_date . " " . $end_time,
            'student_name' => Helper::nameFromCID($cid),
            'facility' => $fac,
            'cbt_required' => $cbt_required,
            'cbt_facility' => $cbt_facility,
            'cbt_block' => $cbt_block
        ];
        $to[] = Helper::emailFromCID($cid);
        if ($instructor > 0) {
            $to[] = Helper::emailFromCID($instructor);
        }
        if ($exam->facility_id != "ZAE") {
            $to[] = $exam->facility_id . "-TA@vatusa.net";
        }

        if ($fac == "ZAE") { $fac = \App\Models\User::find($cid)->facility; }
        EmailHelper::sendEmailFacilityTemplate($to, "Exam Assigned", $fac, "examassigned", $data);

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Exam " . $data['exam_name'] . " assigned, set to expire on $end_date at $end_time.";
        $log->save();
    }

    /**
     * Unassign exam
     *
     * @param string $cid
     * @param int $exam
     */
    public static function unassign($cid, $exam)
    {
        if (!static::isAssigned($cid, $exam))
            return;

        DB::table("exam_assignments")->where("exam_id", $exam)->where("cid", $cid)->delete();
        DB::table("exam_reassignments")->where("exam_id", $exam)->where("cid", $cid)->delete();
        $exam = Exam::find($exam);

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Exam " . $exam->name . " unassigned.";
        $log->save();
    }

    public static function validRetakes()
    {
        return [1, 3, 5, 7, 14, 21, 28, 35];
    }

    public static function expireOptions()
    {
        return [7, 14, 21, 28, 35];
    }

    public static function generateRandomQuestions($examid)
    {
        $list = [];
        $questions = ExamQuestions::where('exam_id',$examid)->orderByRaw("RAND()")->get();
        foreach($questions as $question)
            $list[] = $question->id;

        return $list;
    }

    public static function examCBTComplete($exam)
    {
        if (!$exam->cbt_required) return true;

        $block = TrainingBlock::find($exam->cbt_required);

        foreach ($block->chapters as $ch) {
            if (!CBTHelper::isComplete($ch->id)) return false;
        }

        return true;
    }
}