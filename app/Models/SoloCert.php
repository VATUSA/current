<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoloCert extends Model {
    protected $table = 'solo_certs';

    public function user() {
        return $this->belongsTo(User::class, 'cid', 'cid');
    }
}
