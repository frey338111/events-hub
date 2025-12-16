@component('mail::message')
# Reset your password

Hi {{ $customer->name ?? 'there' }},

We received a request to reset your password. Click the button below to proceed.

@component('mail::button', ['url' => url('/customer/reset-password/' . $token)])
Reset Password
@endcomponent

If you did not request a password reset, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
