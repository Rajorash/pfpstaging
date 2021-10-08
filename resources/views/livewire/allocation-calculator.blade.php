<div class="p-6 sm:px-20">
    <div class="text-lg text-center mb-4">{{__('Allocation Calculator For')}} {{$this->business->name}}
        <select class="form-select rounded px-3 py-1 ml-4 pr-8" name="" id="" wire:model="selectedBusinessId">
            @foreach ($selectOptions as $business_id => $business_name)
                <option value="{{$business_id}}">{{$business_name}}</option>
            @endforeach
        </select>
    </div>
    <x-ui.table>
        <tr>
            <x-ui.th></x-ui.th>
            <x-ui.th></x-ui.th>
            <x-ui.th class="w-10">{{__('Actual')}}</x-ui.th>
            <x-ui.th>{{__('Roll Out %')}}</x-ui.th>
            <x-ui.th>{{__('Allocation $')}}</x-ui.th>
        </tr>
        {{-- Revenue account/s - should only be one? --}}
        @foreach ($mappedAccounts['revenue'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{__('Top line revenue - Account')}} "{{$account['name']}}"</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 border border-gray-300 bg-yellow-100 w-32">
                    <x-ui.input type="text" wire:model="revenue"/>
                </td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
            </tr>
        @endforeach
        {{-- Sales tax account/s --}}
        @foreach ($mappedAccounts['salestax'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 border border-gray-300 bg-indigo-200 text-right">{{$account['percent']}}%</td>
                <td class="text-right px-2 py-1 border border-gray-300 bg-green-300">
                    ${{number_format($account['value'], 0)}}</td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
            </tr>
        @endforeach
        <tr class="">
            <td class="px-2 py-1 border border-gray-300">{{__('Net Cash Receipts')}}</td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="text-right px-2 py-1 border border-gray-300">${{number_format($netCashReceipts, 0)}}</td>
            <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
            <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
        </tr>
        {{-- Pre-real account/s --}}
        @foreach ($mappedAccounts['prereal'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 border border-gray-300 bg-indigo-200 text-right">{{$account['percent']}}%</td>
                <td class="text-right px-2 py-1 border border-gray-300 bg-green-100">
                    ${{number_format($account['value'], 0)}}</td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
                <td class="bg-gray-200 px-2 py-1 border border-gray-300"></td>
            </tr>
        @endforeach
        {{-- Net Cash Receipts --}}
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">{{__('Real Revenue')}}</td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="text-right px-2 py-1 border border-gray-300 bg-green-100">
                ${{number_format($realRevenue, 0)}}</td>
            <td class="text-right px-2 py-1 border border-gray-300 {!! ($postrealPercentageSum > 100 || $postrealPercentageSum < 100) ? 'text-red-500' : '';!!}">{{$postrealPercentageSum}}
                %
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
        </tr>
        {{-- Post-real accounts --}}
        @foreach ($mappedAccounts['postreal'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="bg-green-100px-2 py-1 border border-gray-300"></td>
                <td class="text-right px-2 py-1 border border-gray-300 bg-gray-400">{{$account['percent']}}%</td>
                <td class="text-right px-2 py-1 border border-gray-300 bg-green-200">
                    ${{number_format($account['value'], 0)}}</td>
            </tr>
        @endforeach
        {{-- Check sum --}}
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">
                {{__('Allocation Sum')}}
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="text-right px-2 py-1 border border-gray-300">
                ${{number_format($allocationSum, 2)}}
        </tr>
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">
                {{__('Error Check')}}
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="text-right px-2 py-1 border border-gray-300">
                <span
                    class="{!! round($checksum, 2) == 0 ? '' : 'text-red-500';!!}">${{number_format($checksum, 2)}}</span>
        </tr>
    </x-ui.table>
</div>
