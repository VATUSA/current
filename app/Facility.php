<?php namespace App;

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
}

