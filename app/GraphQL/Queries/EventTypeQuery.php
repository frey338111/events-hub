<?php

namespace App\GraphQL\Queries;

use App\Models\EventsType;

class EventTypeQuery
{
    public function listEventTypes()
    {
        return EventsType::orderBy('name')->get();
    }
}
