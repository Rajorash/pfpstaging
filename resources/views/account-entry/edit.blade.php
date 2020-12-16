@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Enter Account Balances for {{$business->name}}</strong></div>

                <div class="card-body">

                    <x-forms.account-entry :business="$business" />

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
