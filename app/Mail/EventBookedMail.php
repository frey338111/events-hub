<?php

namespace App\Mail;

use App\Models\EventTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventBookedMail extends Mailable
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
        return $this->subject('You have booked the event with us')
            ->markdown('emails.events.booked', [
                'event' => $this->ticket,
            ]);
    }
}
