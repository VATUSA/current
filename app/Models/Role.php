<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'cid', 'cid');
    }


    public function title()
    {
        return $this->hasOne(RoleTitle::class, 'role', 'role');
    }

}
