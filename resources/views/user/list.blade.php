
@extends('layouts.app')

@section('content')
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-2/3 pr-4 pl-4">
            <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900"><strong>Users Visible To You</strong></div>

                <div class="flex-auto p-6">
                @forelse ($users as $user)
                    <a href="/user/{{$user->id}}"><strong>{{ $user->name }}</strong></a><br>
                    <br>
                @empty
                    No users!
                @endforelse
                <br>
                @if(Auth::user()->roles->pluck('name'))
                <a href="/user/create" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded py-1 px-3 leading-normal no-underline bg-teal-500 text-white hover:bg-teal-600">Create Client</a>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
