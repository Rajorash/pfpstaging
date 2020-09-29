@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>Bank Accounts</h1>
            @forelse($accounts as $acc)
            <div class="d-flex justify-content-between align-items-center px-2 py-1" style="background: #cdcdcd">
                <strong class="uppercase">{{ $acc->name }}</strong> <em>{{ $acc->type }}</em> 
                <span class="inline-block text-right">
                    <a  href="/{{ Request::path() }}/{{$acc->id}}/edit" class="btn btn-sm btn-info">Edit</a>
                    <form class="d-inline" action="/{{ Request::path() }}/{{$acc->id}}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </span>
            </div>
            @forelse($acc->flows as $flow)
            <div class="d-flex justify-content-between align-items-center py-2 pl-4 pr-2 text-{{$flow->isNegative() ? 'danger' : 'success' }}">
                {{ $flow->label }}
                <span class="inline-block text-right"><a href="/accounts/{{$acc->id}}/flow/{{$flow->id}}/edit" class="btn btn-sm btn-info">Edit</a> 
                    <form class="d-inline" action="/accounts/{{$acc->id}}/flow/{{$flow->id}}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </span>
            </div>
            @empty
            <div class="py-2 pl-4 pr-2">No flows added.</div>
            @endforelse
            <div class="py-2 pl-4 pr-2" style="border-bottom: 1px #363636 solid;"><a href="/accounts/{{ $acc->id }}/create-flow" class="btn btn-sm btn-success">+ Flow adjustment</a>
            </div>
            @empty
            <div class="d-flex justify-content-between align-items-center px-2 py-1">
            <strong>No accounts created.</strong>
            </div>
            @endforelse
            <div class="mt-2"><a href="/{{ Request::path() }}/create" class="btn btn-sm btn-success">+ New Account</a></div>
        </div>
    </div>
</div>

@endsection