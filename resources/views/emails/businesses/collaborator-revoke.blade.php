@component('mail::message')
    # Collaboration for you

    Collaboration on Business <b>&quot;{{$businessName}}&quot;</b> revoked to you, {{$collaboratorName}}

    @component('mail::button', ['url' => route('businesses')])
        Goto Business
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
