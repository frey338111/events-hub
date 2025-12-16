<?php

namespace App\Services;

use App\Models\Events;
use App\Models\EventTicket;

class EventTicketService
{
    public function initTickets(Events $event)
    {
        $existingCount = EventTicket::where('event_id', $event->id)->count();
        // Optionally delete old tickets
        if ($existingCount > 0) {
            return true;
        }
        for ($i = 0; $i < $event->capacity; $i++) {
            EventTicket::create([
                'event_id' => $event->id,
                'customer_id' => 0,
                'status' => 'open',
                'hash_key' => '',
            ]);
        }

        return true;
    }
}
