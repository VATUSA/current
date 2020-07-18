<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PushLog extends Model {
  protected $table = 'push_log';

  public function user() {
    $this->belongsTo('App\User', 'cid', 'cid');
  }
}

 ?>
