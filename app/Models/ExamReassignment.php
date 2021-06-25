<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamReassignment
    extends Model
{
    public $timestamps = false;
    protected $table = "exam_reassignments";
    protected $dates = ['reassign_date'];
}