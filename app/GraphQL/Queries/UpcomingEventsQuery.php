<?php

namespace App\GraphQL\Queries;

use App\Models\Events;

class UpcomingEventsQuery
{
    public function resolve($_, array $args)
    {
        $query = Events::query()
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc');

        if (! empty($args['customer_id'])) {
            $customerId = $args['customer_id'];

            $query->whereHas('tickets', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        return $query->get();
    }
}
