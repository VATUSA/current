<?php

namespace App\Classes;
use Carbon\Carbon;
use DB;
use Auth;

class ExamHelper
{
    public static function academyPassedExam($cid, $rating, $intervalDays = 0, $intervalMonths = 0): bool
    {
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