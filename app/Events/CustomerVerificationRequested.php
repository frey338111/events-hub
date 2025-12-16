<?php

namespace App\Events;

use App\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerVerificationRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(public Customer $customer) {}
}
