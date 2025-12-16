<?php

namespace App\Listeners;

use App\Events\TicketCanceled;
use App\Jobs\SendTicketCancelEmailJob;

class SendTicketCancelEmail
{
    /**
     * Handle the event.
     */
    public function handle(TicketCanceled $event): void
    {
        $customer = $event->ticket->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        SendTicketCancelEmailJob::dispatch($event->ticket);
    }
}
