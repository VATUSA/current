<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyCategory extends Model
{
    protected $table = "policy_categories";

    protected $dates = ['created_at', 'updated_at'];

    public function policies() {
        return $this->hasMany(Policy::class, 'category')->orderBy('order');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first() ?? abort(404);
    }
}
