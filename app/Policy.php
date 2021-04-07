<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $dates = ['created_at', 'updated_at'];

    public function category() {
        return $this->belongsTo(PolicyCategory::class,'policy_category');
    }
}
