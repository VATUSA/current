<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model {
    protected $table = 'promotions';

    protected $dates = ['created_at', 'updated_at'];

    public function User() {
        $this->belongsTo(User::class, 'cid', 'cid');
    }
}

