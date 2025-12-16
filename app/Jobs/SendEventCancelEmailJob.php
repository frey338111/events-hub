<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Events;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventCancelEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Events $event,
        public Customer $customer
    ) {}

    public function handle(): void
    {
        if (empty($this->customer->email)) {
            return;
        }

        Mail::to($this->customer->email)->send(new \App\Mail\EventCanceledMail($this->event, $this->customer));
    }
}
