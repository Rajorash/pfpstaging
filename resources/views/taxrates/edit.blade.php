@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tax Rates</div>

                <div class="card-body">
                    @forelse ($salestaxAccounts as $acc)
                    <form action="/taxrate" class="form-inline" method="POST">
                        @csrf
                        {!! Form::hidden('account_id', $acc->id) !!}
                        <label for="taxrate" class="ml-auto mr-2 text-right">
                            {{$acc->name}}
                        </label>
                        <div class="input-group mb-2 mr-sm-2">
                        <input type="text" name="rate" class="form-control text-right" id="taxrate" placeholder="10" value="{{$acc->taxRate->rate ?? ''}}">
                            <div class="input-group-append">
                                <div class="input-group-text">%</div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary mb-2">Submit</button>
                    </form>
                    @empty
                    No accounts with 'salestax' designation.
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
