<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tickets";

    public function replies() {
        return $this->hasMany('App\TicketReplies')->orderBy('created_at', 'asc');
    }

    public function lastreply() {
        return $this->hasOne('App\TicketReplies')->orderBy('created_at', 'desc');
    }

    public function notes() {
        return $this->hasMany('App\TicketNotes');
    }

    public function submitter() {
        return $this->hasOne('App\User', 'cid', 'cid');
    }

    public function history() {
        return $this->hasMany('App\TicketHistory', 'ticket_id', 'id')->orderBy('created_at', 'asc');
    }

    public function assignedto() {
        if ($this->assigned_to != 0)
            return $this->hasOne('App\User', 'cid', 'assigned_to');
        else
            return null;
    }

    public function lastreplier() {
        if (count($this->replies) == 0) return false;
        else return $this->lastreply->submitter->fullname();
    }
    public function viewbody() {
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $this->body);
        return nl2br($string, false);
    }
}