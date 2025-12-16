<?php

namespace App\Events;

use App\Models\EventTicket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventBooked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public EventTicket $ticket) {}
}
