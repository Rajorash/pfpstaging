@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <p>
                    You are logged in!
                    </p>

                    <strong>Roles of current user</strong>
                    <ul>
                        @foreach (Auth::user()->roles as $role)
                        <li>{{ $role->name }}</li>
                        @endforeach
                    </ul>


                    <strong>Permissions for current user</strong>
                    <ul>
                        @foreach (Auth::user()->permissions() as $permission)
                        <li>{{ $permission }}</li>
                        @endforeach
                    </ul>

                    @can('see_clients')
                    <strong>Only users who can see clients can see this sentence.</strong>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
