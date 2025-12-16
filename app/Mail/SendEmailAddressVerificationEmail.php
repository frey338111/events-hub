<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailAddressVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Customer $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Build the message.
     */
    public function build()
    {

        return $this->subject('Please verify your email address')
            ->markdown('emails.events.verification', [
                'customer' => $this->customer,
            ]);
    }
}
