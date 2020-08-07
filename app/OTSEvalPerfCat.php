<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OTSEvalPerfCat extends Model
{
    protected $table = "ots_evals_perf_cats";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            return $builder->orderBy('order');
        });
    }

    public function form()
    {
        return $this->belongsTo(OTSEvalForm::class, 'form_id');
    }

    public function indicators()
    {
        return $this->hasMany(OTSEvalPerfInd::class, 'perf_cat_id');
    }

    public function results()
    {
        return $this->hasManyThrough(OTSEvalIndResult::class, OTSEvalPerfInd::class, 'perf_cat_id',
            'perf_indicator_id');
    }

}
