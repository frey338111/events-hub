<?php

namespace App\GraphQL\Queries;

use App\Models\CustomerMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CustomerMessageQuery
{
    public function unread(): Collection
    {
        // Ensure we use the customer guard for JWT-authenticated customers
        $customerId = Auth::guard('customer')->id() ?? Auth::id();

        $query = CustomerMessage::where('customer_id', $customerId)
            ->where('status', 'unread')
            ->orderByDesc('created_at');

        return $query->get();
    }
}
