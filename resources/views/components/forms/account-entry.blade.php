@props([
    'business'
])

@php
    $accounts = $business->accounts->filter(function ($account) {
        return !in_array($account->type, ['revenue']);
    });
@endphp

<x-forms.form
    method="PATCH"
    action="/business/{{$business->id}}/account-entry"
>
    <div class="form-group row">
        <label class="col-sm-4 col-form-label text-right"for="date">Balance date</label>
        <div class="col-sm-8">
        <input class="form-control" name="date" type="date" value="{{ old('date') }}">
        </div>
    </div>
    @forelse ($accounts as $acc)
    <div class="form-group row">
        <label class="col-sm-4 col-form-label text-right" for="amount[{{$acc->id}}]">{{__($acc->name)}}</label>
        <div class="col-sm-8">
        <input class="form-control" name="amount[{{$acc->id}}]" type="text" value="{{old("amount.$acc->id")}}">
        </div>
    </div>
    @empty
    Empty
    @endforelse
    <div class="col-sm-8 offset-sm-4">
        <button class="btn btn-success" type="submit">Submit</button>
    </div>
</x-forms.form>
