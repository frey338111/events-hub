<?php

namespace App\Jobs;

use App\Mail\CustomerPasswordResetMail;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCustomerPasswordResetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Customer $customer, public string $token) {}

    public function handle(): void
    {
        Mail::to($this->customer->email)->send(new CustomerPasswordResetMail($this->customer, $this->token));
    }
}
