<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrainingRecord
 * @package App
 * )
 */
class TrainingRecord extends Model
{
    protected $dates = ['created_at', 'updated_at', 'session_date'];
    protected $casts = ['is_ots'       => 'boolean',
                        'is_cbt'       => 'boolean',
                        'solo_granted' => 'boolean',
                        'ots_result'   => 'boolean'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'cid');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'cid');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'modified_by', 'cid');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function otsEval()
    {
        return $this->hasOne(OTSEval::class, 'training_record_id','ots_eval_id');
        //Optional. No relationship if the eval is created independently.
        //On training record display, search for independent evals (denoted with *) by mapping
        //position to level (APP = S3).
    }

    public function resolveRouteBinding($value)
    {
        return $this->where($this->getRouteKeyName(), $value)->first() ?? abort(404);
    }
}
