@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1>Bank Accounts</h1>
            @forelse($accounts as $acc)
            <div class="d-flex justify-content-between align-items-center px-2 py-1" style="background: #cdcdcd">
            <strong class="uppercase">{{ $acc['name'] }}</strong> <span class="inline-block text-right"><button class="btn btn-sm btn-info">Edit</button> <button class="btn btn-sm btn-danger">Delete</button></span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 pl-4 pr-2 text-success">Inflow <span class="inline-block text-right"><button class="btn btn-sm btn-info">Edit</button> <button class="btn btn-sm btn-danger">Delete</button></span></div>
            <div class="d-flex justify-content-between align-items-center py-2 pl-4 pr-2 text-danger">Outflow <span class="inline-block text-right"><button class="btn btn-sm btn-info">Edit</button> <button class="btn btn-sm btn-danger">Delete</button></span></div>
            <div class="py-2 pl-4 pr-2" style="border-bottom: 1px #363636 solid;"><button class="btn btn-sm btn-success">+ Flow adjustment</button>
            </div>
            @empty
            <div class="d-flex justify-content-between align-items-center px-2 py-1">
            <strong>No accounts created.</strong> <span class="inline-block text-right">
            <button class="btn btn-sm btn-success">Add</button></span>
            </div>
            @endforelse
            <div class="mt-2"><button class="btn btn-sm btn-success">+ New Account</button></div>
        </div>
    </div>
</div>

@endsection