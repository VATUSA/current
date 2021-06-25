<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestions
    extends Model
{
    public $timestamps = false;
    protected $table = "exam_questions";

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}