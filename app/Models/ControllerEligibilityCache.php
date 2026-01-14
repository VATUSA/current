<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ControllerEligibilityCache extends Model
{

    public $primaryKey = "cid";
    protected $table = 'controller_eligibility_cache';

    protected $casts = [
        "is_initial_selection" => "boolean",
        "has_consolidation_hours" => "boolean"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'cid', 'cid');
    }

}