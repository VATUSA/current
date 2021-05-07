<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PolicyCategory extends Model
{
    protected $table = "policy_categories";

    protected $dates = ['created_at', 'updated_at'];

    public function policies() {
        return $this->hasMany(Policy::class, 'category');
    }

    public function resolveRouteBinding($value)
    {
        return $this->where($this->getRouteKeyName(), $value)->first() ?? abort(404);
    }
}
