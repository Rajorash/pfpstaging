@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Update Account For {{$business->name}}</strong></div>

                <div class="card-body">
                    <form method="POST" action="/business/{{$account->business_id}}/accounts/{{$account->id}}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $account->name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Account Type:</label>
                            <div class="col-md-6">
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" >
                                    <option value="">Select your account type</option>
                                @foreach (App\Models\BankAccount::type_list() as $account_index => $account_type)
                                    <option value="{{ $account_index }}"{{ $account_type == $curr_type ? ' selected' : '' }}>{{ $account_type }}</option>
                                @endforeach
                                </select>
                            </div>

                            @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Account') }}
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
