<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyCompetency extends Model {

    protected $table = 'academy_competency';
    public function course()
    {
        return $this->belongsTo(AcademyCourse::class, 'academy_course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cid', 'cid');
    }
}

