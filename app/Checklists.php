<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklists extends Model {
    protected $table = 'checklists';

    public function items() {
        return $this->hasMany('App\ChecklistData','checklist_id','id');
    }
}

