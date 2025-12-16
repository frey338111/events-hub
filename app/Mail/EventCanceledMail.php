<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventCanceledMail extends Mailable
{
    use Queueable, SerializesModels;

    public Events $event;

    public Customer $customer;

    public function __construct(Events $event, Customer $customer)
    {
        $this->event = $event;
        $this->customer = $customer;
    }

    public function build()
    {
        return $this->subject('Event canceled: '.$this->event->title)
            ->markdown('emails.events.event_canceled', [
                'event' => $this->event,
                'customer' => $this->customer,
            ]);
    }
}
