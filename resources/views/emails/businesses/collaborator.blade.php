@component('mail::message')
    # {{$title}}

    {!! $text !!}

    @component('mail::button', ['url' => route('businesses')])
        Goto Business
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
