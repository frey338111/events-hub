@component('mail::message')
# Event Canceled

Hello {{ $customer->name ?? 'there' }},

We’re sorry to let you know that the event **{{ $event->title }}** has been canceled.

@isset($event->start_time)
- **Start:** {{ $event->start_time }}
@endisset
@isset($event->location)
- **Location:** {{ $event->location->name ?? '' }}
@endisset

Thanks for your understanding.

Thanks,<br>
Event Hub Team
@endcomponent
