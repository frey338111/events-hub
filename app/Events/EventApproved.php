<?php

namespace App\Events;

use App\Models\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Events $event) {}
}
