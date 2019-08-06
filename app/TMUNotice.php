<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TMUNotice extends Model
{
    public $timestamps = ['created_at', 'updated_at', 'expire_date', 'start_date'];
    protected $guarded = [];
    protected $table = 'tmu_notices';

    public function tmuFacility()
    {
        return $this->belongsTo(tmu_facilities::class);
    }
}
