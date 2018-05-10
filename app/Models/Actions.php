<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Actions extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'action_log';

    public $timestamps = false;

}

