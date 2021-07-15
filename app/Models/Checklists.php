<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklists extends Model {
    protected $table = 'checklists';

    public function items() {
        return $this->hasMany(ChecklistData::class,'checklist_id','id');
    }
}

