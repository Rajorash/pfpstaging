@component('mail::message')
# New Business for you

Business <b>&quot;{{$businessName}}&quot;</b> linked to you, {{$ownerName}}

@component('mail::button', ['url' => route('businesses')])
Goto Business
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
