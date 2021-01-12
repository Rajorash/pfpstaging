
@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>This User Is</strong></div>

                <div class="flex-auto p-6">
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
