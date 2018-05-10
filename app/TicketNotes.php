<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketNotes extends Model
{
    protected $table = "tickets_notes";

    public function ticket() {
        return $this->belongsTo('App\Ticket', 'id', 'ticket_id');
    }
}