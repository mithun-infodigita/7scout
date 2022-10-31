@component('mail::message')

# Stock update fail




@component('mail::panel')
    Producer: {{ $producer }} <br><br>

    {{ $exception }}
@endcomponent


{{ config('app.name') }}
@endcomponent
