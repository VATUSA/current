<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OTSEval extends Model
{
    protected $table = "ots_evals";

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

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function form()
    {
        return $this->belongsTo(OTSEvalForm::class, 'form_id');
    }

    public function results()
    {
        return $this->hasMany(OTSEvalIndResult::class, 'eval_id');
    }

    /**
     * Eager-load all form elements.
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAll(Builder $query) {
        return $query->with(['form', 'form.perfcats', 'form.perfcats.indicators', 'form.perfcats.indicators.results']);
    }
}
