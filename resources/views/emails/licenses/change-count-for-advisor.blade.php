@component('mail::message')
# Count of licenses was change

{{$author->name}} set new count of licenses for your account.

Total licenses: <b>{{$licensesCounter}}</b><br>
Assigned licenses (in use): <b>{{$assignedLicenses}}</b><br>
Available licenses: <b>{{$availableLicenses}}</b>


@if($availableLicenses < 0)
<span style="color: red;"><b>You haven`t any available licenses. Pls, goto your <a href="{{route('businesses')}}">Business page</a> and remove some active licenses</b></span>
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
