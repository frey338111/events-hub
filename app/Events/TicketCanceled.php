<?php

namespace App\Events;

use App\Models\EventTicket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public EventTicket $ticket) {}
}
