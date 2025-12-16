<?php

namespace App\Jobs;

use App\Mail\EventApprovedMail;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendApprovalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Events $event) {}

    public function handle(): void
    {
        $customer = $this->event->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        Mail::to($customer->email)
            ->send(new EventApprovedMail($this->event));
    }
}
