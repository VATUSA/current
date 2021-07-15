<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAssignment extends Model
{
    public $timestamps = false;
    protected $table = "exam_assignments";
    protected $dates = ['assigned_date', 'expire_date'];

    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id','id')->first();
    }
}