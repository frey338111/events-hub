<?php

namespace App\Listeners;

use App\Events\EventCanceledByCreator;
use App\Jobs\SendEventCancelEmailJob;
use App\Models\EventTicket;

class QueueSendEventCancelNotification
{
    /**
     * Handle the event.
     */
    public function handle(EventCanceledByCreator $event): void
    {
        $tickets = EventTicket::with('customer')
            ->where('event_id', $event->event->id)
            ->get();

        $tickets
            ->pluck('customer')
            ->filter(fn ($customer) => $customer && $customer->email)
            ->unique('id')
            ->each(function ($customer) use ($event) {
                SendEventCancelEmailJob::dispatch($event->event, $customer);
            });
    }
}
