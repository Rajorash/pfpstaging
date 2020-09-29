@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Create A New Flow For {{$account->name}}</strong></div>

                <div class="card-body">
                    <form method="POST" action="/accounts/{{$account->id}}/create-flow">
                        @csrf

                        <div class="form-group row">
                            <label for="label" class="col-md-4 col-form-label text-md-right">{{ __('Label') }}</label>

                            <div class="col-md-6">
                                <input id="label" type="text" class="form-control @error('label') is-invalid @enderror" name="label" value="{{ old('label') }}" required autocomplete="label" autofocus>

                                @error('label')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Account Type:</label>
                            <div class="col-md-6">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="flow-direction" id="flow-in" autocomplete="off" value="false" checked>
                                        In
                                    </label>
                                    <label class="btn btn-danger">
                                        <input type="radio" name="flow-direction" id="flow-out" autocomplete="off" value="true">
                                        Out
                                    </label>
                                </div>
                            </div>
                                
                            @error('account_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Add Flow') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
