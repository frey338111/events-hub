<?php

namespace App\Jobs;

use App\Mail\SendEmailAddressVerificationEmail;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Customer $customer) {}

    public function handle(): void
    {
        if (empty($this->customer->email)) {
            return;
        }

        Mail::to($this->customer->email)
            ->send(new SendEmailAddressVerificationEmail($this->customer));
    }
}
