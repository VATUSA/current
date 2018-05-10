<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public $timestamps = false;
    protected $table = "exams";

    public function questions() {
        return $this->hasMany('App\ExamQuestions', 'exam_id');
    }

    public function facility() {
        return $this->hasOne('App\Facility', 'id', 'facility_id');
    }
}