<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TMUNotice extends Model
{
    public $dates = ['created_at', 'updated_at', 'expire_date', 'start_date'];
    protected $guarded = [];
    protected $table = 'tmu_notices';
    protected $casts = ['is_delay' => 'boolean'];

    public function tmuFacility()
    {
        return $this->belongsTo(tmu_facilities::class);
    }
}
