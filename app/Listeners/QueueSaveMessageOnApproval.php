<?php

namespace App\Listeners;

use App\Events\EventApproved;
use App\Jobs\SaveApprovedEventMessageJob;

class QueueSaveMessageOnApproval
{
    /**
     * Handle the event.
     */
    public function handle(EventApproved $event): void
    {
        SaveApprovedEventMessageJob::dispatch($event->event);
    }
}
