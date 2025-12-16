<?php

namespace App\Mail;

use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Events $event;

    /**
     * Create a new message instance.
     */
    public function __construct(Events $event)
    {
        $this->event = $event;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Event Has Been Approved')
            ->markdown('emails.events.approved', [
                'event' => $this->event,
            ]);
    }
}
