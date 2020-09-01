@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>This business is:</strong></div>

                <div class="card-body">
                    <a href="/business/{{$business->id}}"><strong>{{ $business->name }}</strong></a><br>
                    Owner: <a href="/user/{{$business->owner->id}}">{{$business->owner->name}}</a><br>
                    Advisor: {{$business->license ? $business->license->advisor->name : 'No advisor.'}}<br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
