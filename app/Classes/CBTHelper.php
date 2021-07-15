<?php
namespace App\Classes;

use App\Models\TrainingChapter;
use App\Models\TrainingProgress;
use Auth;

class CBTHelper
{
    public static function isComplete($chapter, $cid = null)
    {
        if (Auth::check()) {
            if ($cid == null) { $cid = Auth::user()->cid; }
            $progress = TrainingProgress::where('cid', $cid)->where('chapterid', $chapter)->first();
            if ($progress != null) return true;
        }

        return false;
    }
}