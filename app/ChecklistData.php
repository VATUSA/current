<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistData extends Model {
    protected $table = 'checklist_data';

    public function checklist() {
        return $this->hasOne('App\Checklists','id','checklist_id');
    }
}

