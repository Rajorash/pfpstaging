@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <h4 class="alert-heading">Success!</h4>
                    <p>{!! \Session::get('success') !!}</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
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
