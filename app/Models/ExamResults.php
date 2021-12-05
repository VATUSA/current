<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResults
    extends Model
{
    public $timestamps = false;
    protected $table = "exam_results";
    protected $casts = ['date' => 'datetime'];

    public function data()
    {
        return $this->hasMany(ExamResultsData::class, 'result_id', 'id');
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'id', 'exam_id');
    }
}