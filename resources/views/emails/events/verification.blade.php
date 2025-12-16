@component('mail::message')
    # Please verify your email address

    Hello {{ $customer->name }},

    Thanks for creating an account with Event Hub. Please confirm your email so we can keep your bookings and updates secure.

    @component('mail::button', ['url' => url('/customer/verify/'.$customer->hash_key)])
        Verify Email
    @endcomponent

    If you didn’t request this, you can safely ignore this email.

    Thanks,
    Event Hub Team
@endcomponent
