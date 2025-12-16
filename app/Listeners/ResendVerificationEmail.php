<?php

namespace App\Listeners;

use App\Events\CustomerVerificationRequested;
use App\Jobs\SendVerificationEmailJob;

class ResendVerificationEmail
{
    /**
     * Handle the event.
     */
    public function handle(CustomerVerificationRequested $event): void
    {
        if (empty($event->customer->email)) {
            return;
        }

        SendVerificationEmailJob::dispatch($event->customer);
    }
}
