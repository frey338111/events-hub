<?php

namespace App\Listeners;

use App\Events\EventApproved;
use App\Jobs\SendApprovalEmailJob;

class SendApprovalEmail
{
    /**
     * Handle the event.
     */
    public function handle(EventApproved $event): void
    {
        if (! $event->event->customer || ! $event->event->customer->email) {
            return;
        }

        SendApprovalEmailJob::dispatch($event->event);
    }
}
