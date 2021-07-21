@component('mail::message')
# Hello!

{{$author->name}} added you to PFP.

Your email: {{$user->email}}
<br />Your password: {{$generatedPassword}}

<span style="color: red;"><b>Important! After login - change your password!</b></span>

@component('mail::button', ['url' => $verifyUrl])
Verify Email Address
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}


@slot('subcopy')
@lang(
    "If you're having trouble clicking the \"Verify Email Address\" button, copy and paste the URL below\n".
    'into your web browser:'
) <span class="break-all">[{{ $verifyUrl }}]({{ $verifyUrl }})</span>
@endslot

@endcomponent
