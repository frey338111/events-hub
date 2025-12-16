<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer, public string $token) {}

    public function build(): self
    {
        return $this->subject('Reset your password')
            ->markdown('emails.customer.password_reset', [
                'customer' => $this->customer,
                'token' => $this->token,
            ]);
    }
}
