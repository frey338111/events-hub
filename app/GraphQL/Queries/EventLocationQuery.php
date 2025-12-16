<?php

namespace App\GraphQL\Queries;

use App\Models\EventsLocation;

class EventLocationQuery
{
    public function listEventLocation()
    {
        return EventsLocation::orderBy('name')->get();
    }
}
