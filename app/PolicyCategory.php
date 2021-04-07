<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PolicyCategory extends Model
{
    protected $table = "policy_categories";

    protected $dates = ['created_at', 'updated_at'];

    public function policies() {
        return $this->hasMany(Policy::class, 'policy_category');
    }
}
