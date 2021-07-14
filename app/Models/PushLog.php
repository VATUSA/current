<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushLog extends Model {
  protected $table = 'push_log';

  public function user() {
    $this->belongsTo(User::class, 'cid', 'cid');
  }
}
