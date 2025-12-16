<?php

namespace App\Mail;

use App\Models\EventTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCanceledEmail extends Mailable
{
    use Queueable, SerializesModels;

    public EventTicket $ticket;

    /**
     * Create a new message instance.
     */
    public function __construct(EventTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your event ticket has been canceled')
            ->markdown('emails.events.canceled', [
                'event' => $this->ticket,
            ]);
    }
}
