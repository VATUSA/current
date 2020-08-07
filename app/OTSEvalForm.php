<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTSEvalForm extends Model
{
    protected $table = "ots_evals_forms";

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }

    public function perfcats()
    {
        return $this->hasMany(OTSEvalPerfCat::class, 'form_id');
    }

    public function indicators()
    {
        return $this->hasManyThrough(OTSEvalPerfInd::class, OTSEvalPerfCat::class, 'form_id','perf_cat_id');
    }

    public function results()
    {
        return $this->hasManyThrough(OTSEvalIndResult::class, OTSEvalPerfInd::class,'form_id','perf_indicator_id');
    }

    /**
     * Scope  to only include active forms.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('active', 1);
    }
}
