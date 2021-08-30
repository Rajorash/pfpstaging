@component('mail::message')
# Business deleted

{!! $string !!}

@component('mail::button', ['url' => route('businesses')])
Goto Business
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
