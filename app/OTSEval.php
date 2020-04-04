<?php

namespace App;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

/**
 * Class OTSEval
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="otseval",
 *     @SWG\Property(property="id", type="integer", description="Record ID"),
 *     @SWG\Property(property="filename", type="string", description="Filename in system"),
 *     @SWG\Property(property="training_record_id", type="integer", description="Training record DB ID, if exists"),
 *     @SWG\Property(property="student_id", type="integer", description="Student CID"),
 *     @SWG\Property(property="instructor_id", type="integer", description="Instructor CID"),
 *     @SWG\Property(property="rating_id", type="integer", description="DB ID of rating")
 * )
 */
class OTSEval extends Model
{
    public function trainingRecord()
    {
        return $this->belongsTo(TrainingRecord::class);
        //Optional. No relationship if the eval is created independently.
        //On training record display, search for independent evals (denoted with *) by mapping
        //position to level (APP = S3).
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'cid');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'cid');
    }

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }

    public function getContent()
    {
        //TODO might need more here
        try {
            $content = File::get(storage_path('app/otsEvals/' . $this->filename . '.json'));
        } catch (FileNotFoundException $e) {
            $content = null;
        }

        return $content;
    }
}
