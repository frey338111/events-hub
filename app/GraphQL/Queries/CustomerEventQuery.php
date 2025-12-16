<?php

namespace App\GraphQL\Queries;

use App\Models\Events;

class CustomerEventQuery
{
    public function createdByCustomer(): \Illuminate\Support\Collection
    {
        $customerId = auth()->id();

        return Events::with(['type', 'location'])
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();
    }
}
