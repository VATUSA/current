<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        return $this->hasManyThrough(OTSEvalPerfInd::class, OTSEvalPerfCat::class, 'form_id', 'perf_cat_id');
    }

    public function results()
    {
        return $this->hasManyThrough(OTSEvalIndResult::class, OTSEvalPerfInd::class, 'form_id', 'perf_indicator_id');
    }

    public function evaluations()
    {
        return $this->hasMany(OTSEval::class, 'form_id');
    }

    public function getStatSparkline( $facility)
    {
        $vals = [];
        $evals = $this->evaluations();
        if ($facility) {
            $evals->where('facility_id', $facility);
        }
        $evals = $evals->orderBy('exam_date', 'DESC')->limit(15)->get();
        foreach ($evals as $eval) {
            $vals[] = $eval->result ? 1 : -1;
        }

        return implode(",", array_reverse($vals));
    }

    /**
     * Scope to only include active forms.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope to only include non-statements.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoStatements($query) {
        return $query->where('is_statement', 0);
    }

    /**
     * Eager-load all form elements.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAll(Builder $query)
    {
        return $query->with(['perfcats', 'perfcats.indicators', 'perfcats.indicators.results']);
    }
}
