@component('mail::message')
    # Your Ticket has been canceled

    Hello {{ $ticket->customer->name }},

    Your ticket for event **{{ $ticket->event->title }}** has been canceled.

    **Event Start:** {{ $ticket->event->start_time }}
    **Location:** {{ $ticket->event->location->name }}

    @component('mail::button', ['url' => url('/events/'.$ticket->event->url_key)])
        View Event
    @endcomponent

    Thanks,
    Event Hub Team
@endcomponent
