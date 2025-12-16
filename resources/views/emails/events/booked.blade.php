@component('mail::message')
    # Your Event Has Been Booked

    Hello {{ $ticket->customer->name }},

    Your event **{{ $ticket->event->title }}** has been booked and we are looking forward to see you.

    **Event Start:** {{ $ticket->event->start_time }}
    **Location:** {{ $ticket->event->location->name }}

    @component('mail::button', ['url' => url('/events/'.$ticket->event->url_key)])
        View Event
    @endcomponent

    Thanks,
    Event Hub Team
@endcomponent
