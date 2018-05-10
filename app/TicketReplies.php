<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketReplies extends Model
{
    protected $table = "tickets_replies";

    public function ticket() {
        return $this->belongsTo('App\Ticket', 'id', 'ticket_id');
    }
    public function submitter() {
        return $this->hasOne('App\User', 'cid', 'cid');
    }
    public function viewbody() {
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $this->body);
        return nl2br($string, false);
    }
}