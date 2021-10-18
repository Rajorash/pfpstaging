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
    <thead>
    <tr class="border-t border-b border-light_blue">
        <x-ui.table-th padding="pl-12 pr-2 py-4 text-right">{{__('Account')}}</x-ui.table-th>
        <x-ui.table-th>{{__('Current Balance')}}</x-ui.table-th>
    </tr>
    </thead>
    <x-ui.table-tbody>
        @forelse ($accounts as $acc)
            <tr class="pt-2 border-0 border-transparent">
                <td class="pt-1 text-right">
                    <label class="pt-2 pb-2 pl-4 pr-4 mb-0 leading-normal text-right sm:w-1/3"
                           for="amounts[{{$acc->id}}]">{{__($acc->name)}}</label>
                </td>
                <td class="pt-1 pr-24">
                    <input
                        class="block w-full px-2 py-1 mb-1 text-base leading-normal text-gray-800 bg-white border border-gray-200 rounded appearance-none"
                        name="amounts[{{$acc->id}}]" type="text" value="{{old("amounts.$acc->id")}}">
                </td>
            </tr>
        @empty
            {{__('No accounts currently created.')}}
        @endforelse
        <tr class="border-0 border-transparent">
            <td class="pt-1"></td>
            <td class="pt-1">
                <button type="submit" class='px-6 py-2 font-normal leading-normal text-center text-white no-underline whitespace-no-wrap border rounded-lg select-none bg-blue hover:bg-dark_gray2'>
                    {{__('Submit Balances')}}
                </button>
            </td>
        </tr>
    </x-ui.table-tbody>
</x-forms.form>
