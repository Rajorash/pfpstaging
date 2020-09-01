
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>This User Is</strong></div>

                <div class="card-body">
                    <strong>{{ $user->name }}</strong><br>
                    Email: {{ $user->email }}<br>
                    Last login: {{ $user->last_login_at ?: 'Unknown' }}<br>
                    <br>
                    <a href="/user">Back to User list</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
