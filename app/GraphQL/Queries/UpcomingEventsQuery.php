<?php

namespace App\GraphQL\Queries;

use App\Models\Events;

class UpcomingEventsQuery
{
    public function resolve($_, array $args)
    {
        $customerId = $args['customer_id'];

        return Events::whereHas('tickets', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();
    }
}
