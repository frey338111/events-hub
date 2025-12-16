<?php

namespace App\Jobs;

use App\Models\CustomerMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SaveApprovedEventMessageJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function handle(): void
    {
        CustomerMessage::create([
            'title' => 'Event Approved',
            'message' => sprintf('your event %s is approved by admin', $this->event->title),
            'customer_id' => $this->event->customer_id,
        ]);
    }
}
