<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnPaths extends Model
{
    protected $dates = ['created_at', 'updated_at'];

    protected $guarded = [];

    public function facility() {
        return $this->belongsTo(Facility::class);
    }
}
