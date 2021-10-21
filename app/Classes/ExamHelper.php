<?php

namespace App\Classes;

use App\Mail\ExamAssigned;
use App\Models\ExamAssignment;
use App\Models\ExamQuestions;
use App\Models\Exam;
use App\Models\Actions;
use App\Models\Facility;
use App\Models\TrainingBlock;
use App\Models\User;
use Carbon\Carbon;
use DB;
use DateTime;
use Auth;
use Illuminate\Support\Facades\Mail;

define("BASIC_EXAM", 7);

class ExamHelper
{
    /**
     * Check if exam is assigned or scheduled for reassignment
     *
     * @param           $cid
     * @param           $exam
     * @param bool|true $incl_reassign
     *
     * @return bool
     */
    public static function isAssigned($cid, $exam, $incl_reassign = true)
    {
        $as = $ras = 0;

        $as = DB::table("exam_assignments")->where("exam_id", $exam)->where("cid", $cid)->count();
        if ($incl_reassign) {
            $ras = DB::table("exam_reassignments")->where("exam_id", $exam)->where("cid", $cid)->count();
        }

        return (($as >= 1) || ($ras >= 1)) ? true : false;
    }

    /**
     * Assign exam
     *
     * @param     $cid
     * @param     $exam
     * @param int $expire_period
     */
    public static function assign($cid, $exam, $instructor = null, $expire_period = 7, $force = false)
    {
        if (static::isAssigned($cid, $exam) && !$force) {
            return;
        }

        if ($instructor == null) {
            $instructor = Auth::user()->cid;
        }

        if (!in_array($expire_period, ExamHelper::expireOptions())) {
            $expire_period = 7;
        }

        $date = new DateTime();
        $date->modify("+$expire_period day");
        $end_date = $date->format("m/d/Y");
        $end_time = $date->format("h:ia");

//        DB::table('exam_assignments')->insert(
//            [
//                'cid'           => $cid,
//                'exam_id'       => $exam,
//                'instructor_id' => $instructor,
//                'assigned_date' => DB::raw('NOW()'),
//                'expire_date'   => DB::raw("DATE_ADD(NOW(), INTERVAL $expire_period DAY)")
//            ]
//        );


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
            'exam_name'       => "(" . $exam->facility_id . ") " . $exam->name,
            'instructor_name' => Helper::nameFromCID($instructor),
            'end_date'        => $end_date . " " . $end_time,
            'student_name'    => Helper::nameFromCID($cid),
            'facility'        => $fac,
            'cbt_required'    => $cbt_required,
            'cbt_facility'    => $cbt_facility,
            'cbt_block'       => $cbt_block
        ];

        $notify = new VATUSADiscord();
        $to = array();
        $staffIds = array();
        $instructor = User::find($instructor);
        $student = User::find($cid);
        $facility = Facility::find($fac);
        $ta = $facility->ta();
        if (!$ta) {
            $ta = $facility->datm();
        }
        if (!$ta) {
            $ta = $facility->atm();
        }
        if (!$ta || $ta->cid == $instructor->cid) {
            $ta = null;
        }

        if ($notify->userWantsNotification($student, "legacy_exam_assigned", "email")) {
            $to[] = Helper::emailFromCID($cid);
        }
        if ($notify->userWantsNotification($instructor, "legacy_exam_assigned", "email")) {
            $to[] = $instructor->email;
        }
        if ($exam->facility_id != "ZAE") {
            if ($ta && $notify->userWantsNotification($ta, "legacy_exam_assigned", "email")) {
                $to[] = $exam->facility_id . "-TA@vatusa.net";
            }
        }

        if ($fac == "ZAE") {
            $fac = $student->facility;
        }

        Mail::to($to)->queue(new ExamAssigned($data));


        if ($notify->userWantsNotification($student, "legacy_exam_assigned", "discord")) {
            $student_id = $student->discord_id;
        } else {
            $student_id = 0;
        }
        if ($ta && $notify->userWantsNotification($ta, "legacy_exam_assigned", "discord")) {
            $staff_id = $ta->discord_id;
        } else {
            $staff_id = 0;
        }
        if ($student_id || $staff_id) {
            $notify->sendNotification('legacyExamAssigned', "dm",
                array_merge($data, compact('staff_id', 'student_id')));
        }
        if ($channel = $notify->getFacilityNotificationChannel($facility, "legacy_exam_assigned")) {
            $notify->sendNotification("legacyExamAssigned", "channel",
                array_merge($data, ['guildId' => $facility->discord_guild, 'channelId' => $channel]));
        }

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Exam " . $data['exam_name'] . " assigned, set to expire on $end_date at $end_time.";
        $log->save();
    }

    /**
     * Unassign exam
     *
     * @param string $cid
     * @param int    $exam
     */
    public
    static function unassign(
        $cid,
        $exam
    ) {
        if (!static::isAssigned($cid, $exam)) {
            return;
        }

        DB::table("exam_assignments")->where("exam_id", $exam)->where("cid", $cid)->delete();
        DB::table("exam_reassignments")->where("exam_id", $exam)->where("cid", $cid)->delete();
        $exam = Exam::find($exam);

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Exam " . $exam->name . " unassigned.";
        $log->save();
    }

    public
    static function validRetakes()
    {
        return [1, 3, 5, 7, 14, 21, 28, 35];
    }

    public
    static function expireOptions()
    {
        return [7, 14, 21, 28, 35];
    }

    public
    static function generateRandomQuestions(
        $examid
    ) {
        $list = [];
        $questions = ExamQuestions::where('exam_id', $examid)->orderByRaw("RAND()")->get();
        foreach ($questions as $question) {
            $list[] = $question->id;
        }

        return $list;
    }

    public
    static function examCBTComplete(
        $exam
    ) {
        if (!$exam->cbt_required) {
            return true;
        }

        $block = TrainingBlock::find($exam->cbt_required);

        foreach ($block->chapters as $ch) {
            if (!CBTHelper::isComplete($ch->id)) {
                return false;
            }
        }

        return true;
    }

    public
    static function academyPassedExam(
        $cid,
        $rating,
        $intervalDays = 0,
        $intervalMonths = 0
    ): bool {
        $moodle = new VATUSAMoodle();
        $config = config("exams." . strtoupper($rating));
        if (!$config || empty($config)) {
            return false;
        }
        $attempts = $moodle->getQuizAttempts($config['id'], $cid);
        if (!$attempts || !is_array($attempts) || empty($attempts)) {
            return false;
        }
        foreach ($attempts as $attempt) {
            if ($attempt['state'] === "finished"
                && $attempt['grade'] >= $config['passingPercent']) {
                if ($intervalDays && $attempt['timefinish'] < time() - $intervalDays * 86400) {
                    continue;
                }
                if ($intervalMonths && Carbon::now()->diffInMonths(Carbon::createFromTimestampUTC($attempt['timefinish'])) > $intervalMonths) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }
}