
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Users Visible To You</strong></div>

                <div class="card-body">
                @forelse ($users as $user)
                    <a href="/user/{{$user->id}}"><strong>{{ $user->name }}</strong></a><br>
                    <br>
                @empty
                    No users!
                @endforelse
                <br>
                @if(Auth::user()->roles->pluck('name'))
                <a href="/user/create" class="btn btn-info">Create Client</a>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
