@component('mail::message')
# Collaboration for you

Business <b>&quot;{{$businessName}}&quot;</b> linked to you, {{$collaboratorName}}

@component('mail::button', ['url' => route('businesses')])
Goto Business
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
