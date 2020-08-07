<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OTSEvalPerfInd extends Model
{
    protected $table = "ots_evals_perf_indicators";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            return $builder->orderBy('order');
        });
    }

    public function perfcat()
    {
        return $this->belongsTo(OTSEvalPerfCat::class,'perf_cat_id');
    }

    public function results()
    {
        return $this->hasMany(OTSEvalIndResult::class,'perf_indicator_id');
    }
}
