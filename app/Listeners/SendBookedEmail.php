<?php

namespace App\Listeners;

use App\Events\EventBooked;
use App\Jobs\SendBookedEmailJob;

class SendBookedEmail
{
    /**
     * Handle the event.
     */
    public function handle(EventBooked $event): void
    {
        $customer = $event->ticket->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        SendBookedEmailJob::dispatch($event->ticket);
    }
}
