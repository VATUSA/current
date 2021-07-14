<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistData extends Model {
    protected $table = 'checklist_data';

    public function checklist() {
        return $this->hasOne(Checklists::class,'id','checklist_id');
    }
}

