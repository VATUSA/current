<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketClosed extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $by;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, string $by)
    {
        $this->ticket = $ticket;
        $this->by = $by;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('support@vatusa.net', 'VATUSA Help Desk')
            ->subject('Ticket Closed')
            ->view('emails.help.closed');
    }
}
