<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTSEvalIndResult extends Model
{
    protected $table = "ots_evals_indicator_results";

    public function indicator()
    {
        return $this->belongsTo(OTSEvalPerfInd::class, 'perf_indicator_id');
    }

    public function otsEval()
    {
        return $this->belongsTo(OTSEval::class, 'eval_id');
    }

    public static function getPercentages(int $indicator, $facility, $instructor, int $interval)
    {
        $result = self::selectRaw('ots_evals_indicator_results.*')->where('perf_indicator_id', $indicator)->leftJoin('ots_evals', 'eval_id', '=', 'ots_evals.id');
        if ($instructor) {
            $result->where('ots_evals.instructor_id', $instructor);
        }
        if ($facility) {
            $result->where('ots_evals.facility_id', $facility);
        }
        $result = $result->orderBy('ots_evals_indicator_results.created_at', 'DESC')->take($interval)->get();
        $total = $result->count();
        $return = [];

        if ($total) {
            for ($i = 0; $i < 4; $i++) {
                $filter = $result->filter(function ($r) use ($i) {
                    return $r->result == $i;
                })->count();
                $return[] = round($filter / $total * 100);
            }
        } else {
            $return = [0, 0, 0, 0];
        }

        return $return;
    }
}
