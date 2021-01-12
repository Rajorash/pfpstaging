@extends('layouts.app')

@section('content')
<x-business-nav businessId="{{$business->id}}" />
<div class="container mx-auto sm:px-4 max-w-full mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <div class="md:w-1/2 pr-4 pl-4">
            <h1>Bank Accounts</h1>
            @forelse($accounts as $acc)
            <div class="flex justify-between items-center px-2 py-1" style="background: #cdcdcd;border-top: 1px #363636 solid;">
                <strong class="uppercase">{{ $acc->name }}</strong> <em>{{ $acc->type }}</em>
                <span class="inline-block text-right">
                    <a href="/accounts/{{ $acc->id }}/create-flow" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-green-500 text-white hover:green-600 mr-1">+ Flow</a>
                    <a  href="/{{ Request::path() }}/{{$acc->id}}/edit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-teal-500 text-white hover:bg-teal-600 mr-1">Edit</a>
                    <form class="inline" action="/{{ Request::path() }}/{{$acc->id}}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-red-600 text-white hover:bg-red-700">Delete</button>
                    </form>
                </span>
            </div>
            @forelse($acc->flows as $flow)
            <div class="flex justify-between items-center py-2 pl-4 pr-2 text-{{$flow->isNegative() ? 'danger' : 'success' }}">
                {{ $flow->label }}
                <span class="inline-block text-right"><a href="/accounts/{{$acc->id}}/flow/{{$flow->id}}/edit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-teal-500 text-white hover:bg-teal-600 mr-1">Edit</a>
                    <form class="inline" action="/accounts/{{$acc->id}}/flow/{{$flow->id}}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-red-600 text-white hover:bg-red-700">Delete</button>
                    </form>
                </span>
            </div>
            @empty
            <div class="py-2 pl-4 pr-2">No flows added.</div>
            @endforelse

            @empty
            <div class="flex justify-between items-center px-2 py-1">
            <strong>No accounts created.</strong>
            </div>
            @endforelse
            <div class="mt-2"><a href="/{{ Request::path() }}/create" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap rounded  no-underline py-1 px-2 leading-tight text-xs  bg-green-500 text-white hover:green-600">+ New Account</a></div>
        </div>
    </div>
</div>

@endsection
