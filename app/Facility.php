<?php namespace App;

use App\Classes\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';
    public $timestamps = false;
    public $incrementing = false;

    public function members()
    {
        return $this->hasMany('App\User', 'facility', 'id');
    }

    public function staffPosition($position)
    {
        return $this->hasOne('App\User', 'cid', $position)->get();
    }

    public function atm()
    {
        return $this->hasOne('App\User', 'cid', 'atm')->first();
    }

    public function datm()
    {
        return $this->hasOne('App\User', 'cid', 'datm')->first();
    }

    public function ta()
    {
        return $this->hasOne('App\User', 'cid', 'ta')->first();
    }

    public function ec()
    {
        return $this->hasOne('App\User', 'cid', 'ec')->first();
    }

    public function fe()
    {
        return $this->hasOne('App\User', 'cid', 'fe')->first();
    }

    public function wm()
    {
        return $this->hasOne('App\User', 'cid', 'wm')->first();
    }

    public function returnPaths()
    {
        return $this->hasMany(ReturnPaths::class);
    }

    public function evaluations()
    {
        return $this->hasMany(OTSEval::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

    public static function getFacTrainingStaff($facility)
    {
        $ins = ['ins' => [], 'mtr' => []];
        $users = User::where('facility', $facility)->where('rating', '>=', Helper::ratingIntFromShort("I1"))
            ->where('rating', '<=', Helper::ratingIntFromShort("I3"))->get();
        if ($users) {
            foreach ($users as $user) {
                $ins['ins'][] = [
                    'cid'  => $user->cid,
                    'name' => $user->fullname()
                ];
            }
        }
        $users = Role::where('facility', $facility)->where('role', 'INS')->get();
        if ($users) {
            foreach ($users as $user) {
                $ins['ins'][] = [
                    'cid'  => $user->cid,
                    'name' => $user->user->fullname()
                ];
            }
        }
        $users = Role::where('facility', $facility)->where('role', 'MTR')->get();
        if ($users) {
            foreach ($users as $user) {
                $ins['mtr'][] = [
                    'cid'  => $user->cid,
                    'name' => $user->user->fullname()
                ];
            }
        }
        foreach ($ins as $k => $v) {
            usort($ins[$k], function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        return $ins;
    }
}

