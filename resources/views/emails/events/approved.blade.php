@component('mail::message')
    # Your Event Has Been Approved

    Hello {{ $event->customer->name }},

    Your event **{{ $event->title }}** has been approved and is now live on the Event Hub.

    **Event Start:** {{ $event->start_time }}
    **Location:** {{ $event->location->name }}

    @component('mail::button', ['url' => url('/events/'.$event->url_key)])
        View Event
    @endcomponent

    Thanks,
    Event Hub Team
@endcomponent
