@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>This business is:</strong></div>

                <div class="flex-auto p-6">
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
