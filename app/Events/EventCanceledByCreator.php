<?php

namespace App\Events;

use App\Models\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCanceledByCreator
{
    use Dispatchable, SerializesModels;

    public function __construct(public Events $event) {}
}
