@props([
    'business'
])

@php
    $accounts = $business->accounts->filter(function ($account) {
        return !in_array($account->type, ['revenue']);
    });
@endphp

<div class="lg:w-1/2 mx-auto">
<x-forms.form
    method="PATCH"
    action="/business/{{$business->id}}/account-entry"
>
    @forelse ($accounts as $acc)
    <div class="mb-4 flex flex-wrap ">
        <label class="sm:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal text-right" for="amount[{{$acc->id}}]">{{__($acc->name)}}</label>
        <div class="sm:w-2/3 pr-4 pl-4">
        <input class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded" name="amount[{{$acc->id}}]" type="text" value="{{old("amount.$acc->id")}}">
        </div>
    </div>
    @empty
    Empty
    @endforelse
    <x-ui.button-normal class="text-center" href="{{route('users.create')}}">
        Submit Balances
    </x-ui.button-normal>

</x-forms.form>
</div>
