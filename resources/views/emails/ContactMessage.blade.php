@component('mail::message')
    # Message From Contact Form

    Name: {{ $body['name'] }}
    Email: {{ $body['email'] }}
    Message:
    {{ $body['message'] }}

    Thanks,
    {{ config('app.name') }}
@endcomponent
