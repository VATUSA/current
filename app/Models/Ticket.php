<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tickets";

    public function replies() {
        return $this->hasMany(TicketReplies::class)->orderBy('created_at', 'asc');
    }

    public function lastreply() {
        return $this->hasOne(TicketReplies::class)->orderBy('created_at', 'desc');
    }

    public function notes() {
        return $this->hasMany(TicketNotes::class);
    }

    public function submitter() {
        return $this->hasOne(User::class, 'cid', 'cid');
    }

    public function history() {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'id')->orderBy('created_at', 'asc');
    }

    public function assignedto() {
        if ($this->assigned_to != 0)
            return $this->hasOne(User::class, 'cid', 'assigned_to');
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