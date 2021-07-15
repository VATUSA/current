<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketNotes extends Model
{
    protected $table = "tickets_notes";

    public function ticket() {
        return $this->belongsTo(Ticket::class, 'id', 'ticket_id');
    }
}