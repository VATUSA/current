<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model {
    protected $table = 'promotions';

    protected $dates = ['created_at', 'updated_at'];

    public function User() {
        $this->belongsTo('\App\User', 'cid', 'cid');
    }
}

