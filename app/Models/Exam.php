<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public $timestamps = false;
    protected $table = "exams";

    public function questions() {
        return $this->hasMany(ExamQuestions::class, 'exam_id');
    }

    public function facility() {
        return $this->hasOne(Facility::class, 'id', 'facility_id');
    }
}