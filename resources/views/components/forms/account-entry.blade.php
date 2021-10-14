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
    <tr class="border-light_blue border-t border-b">
        <x-ui.table-th padding="pl-12 pr-2 py-4 text-right">{{__('Account')}}</x-ui.table-th>
        <x-ui.table-th>{{__('Current Balance')}}</x-ui.table-th>
    </tr>
    </thead>
    <x-ui.table-tbody>
        @forelse ($accounts as $acc)
            <tr class="border-transparent border-0 pt-2">
                <td class="pt-1 text-right">
                    <label class="sm:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal text-right"
                           for="amounts[{{$acc->id}}]">{{__($acc->name)}}</label>
                </td>
                <td class="pt-1 pr-24">
                    <input
                        class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-800 border border-gray-200 rounded"
                        name="amounts[{{$acc->id}}]" type="text" value="{{old("amounts.$acc->id")}}">
                </td>
            </tr>
        @empty
            {{__('No accounts currently created.')}}
        @endforelse
        <tr class="border-transparent border-0">
            <td class="pt-1"></td>
            <td class="pt-1">
                <button type="submit" class='text-center select-none border font-normal whitespace-no-wrap
               rounded-lg py-2 px-6 leading-normal no-underline bg-blue text-white hover:bg-dark_gray2'>
                    {{__('Submit Balances')}}
                </button>
            </td>
        </tr>
    </x-ui.table-tbody>
</x-forms.form>
