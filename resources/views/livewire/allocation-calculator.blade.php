<div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    <div class="text-lg font-semibold text-center">Allocation Calculator For {{$this->business->name}}
        <select class="rounded pl-3 pr-8 py-0 mx-3 my-0" name="" id=""  wire:model="selectedBusinessId">
            @foreach ($selectOptions as $business_id => $business_name)
            <option value="{{$business_id}}">{{$business_name}}</option>
            @endforeach
        </select>
    </div>
    <x-ui.table>
        <tr>
            <x-ui.th></x-ui.th>
            <x-ui.th></x-ui.th>
            <x-ui.th class="w-10">Actual</x-ui.th>
            <x-ui.th>Roll Out %</x-ui.th>
            <x-ui.th>Allocation $</x-ui.th>
        </tr>
        {{-- Revenue account/s - should only be one? --}}
        @foreach ($mappedAccounts['revenue'] as $account)
        <tr>
            <td class="px-3 py-1">Top line revenue - Account "{{$account['name']}}"</td>
            <td class="px-3 py-1 w-32"></td>
            <td class="px-3 py-1 bg-yellow-300 w-32"><x-ui.input type="text" wire:model="revenue" /></td>
            <td class="bg-gray-500 w-32"></td>
            <td class="bg-gray-500 w-32"></td>
        </tr>
        @endforeach
        {{-- Sales tax account/s --}}
        @foreach ($mappedAccounts['salestax'] as $account)
        <tr>
            <td class="px-3 py-1">{{$account['name']}}</td>
            <td class="px-3 py-1 bg-blue text-right">{{$account['percent']}}%</td>
            <td class="text-right px-3 py-1 bg-green-300">${{number_format($account['value'], 0)}}</td>
            <td class="bg-gray-500"></td>
            <td class="bg-gray-500"></td>
        </tr>
        @endforeach
        <tr class="border border-blue bg-dark_gray2">
            <td class="px-3 py-1">Net Cash Receipts</td>
            <td class="px-3 py-1"></td>
            <td class="text-right px-3 py-1">${{number_format($netCashReceipts, 0)}}</td>
            <td class="bg-gray-500 border border-gray-500"></td>
            <td class="bg-gray-500 border border-gray-500"></td>
        </tr>
        {{-- Pre-real account/s --}}
        @foreach ($mappedAccounts['prereal'] as $account)
        <tr>
            <td class="px-3 py-1">{{$account['name']}}</td>
            <td class="px-3 py-1 bg-blue text-right">{{$account['percent']}}%</td>
            <td class="text-right px-3 py-1 bg-green-300">${{number_format($account['value'], 0)}}</td>
            <td class="bg-gray-500"></td>
            <td class="bg-gray-500"></td>
        </tr>
        @endforeach
        {{-- Net Cash Receipts --}}
        <tr class="border border-blue bg-dark_gray2">
            <td class="px-3 py-1">Real Revenue</td>
            <td class="px-3 py-1"></td>
            <td class="text-right px-3 py-1 bg-green-100">${{number_format($realRevenue, 0)}}</td>
            <td class="text-right px-3 py-1 {!! ($postrealPercentageSum > 100 || $postrealPercentageSum < 100) ? 'text-red-500' : '';!!}">{{$postrealPercentageSum}}%</td>
            <td class="bg-gray-500 border border-gray-500"></td>
        </tr>
        {{-- Post-real accounts --}}
        @foreach ($mappedAccounts['postreal'] as $account)
        <tr>
            <td class="px-3 py-1">{{$account['name']}}</td>
            <td class="px-3 py-1"></td>
            <td class="bg-green-100"></td>
            <td class="text-right px-3 py-1 bg-dark_gray2">{{$account['percent']}}%</td>
            <td class="text-right px-3 py-1 bg-green-300">${{number_format($account['value'], 0)}}</td>
        </tr>
        @endforeach
        {{-- Check sum --}}
        <tr class="bg-gray-100 border border-gray-200">
            <td class="px-3 py-1">
                Allocation Sum<br>
                Error Check
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right px-3 py-1">
                ${{number_format($allocationSum, 2)}}<br>
                <span class="{!! round($checksum, 2) == 0 ? '' : 'text-red-500';!!}">${{number_format($checksum, 2)}}</span>
        </tr>


    </x-ui.table>
</div>
