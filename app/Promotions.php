<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model {
    protected $table = 'promotions';

    public function User() {
        $this->belongsTo('\App\User', 'cid', 'cid');
    }
}

