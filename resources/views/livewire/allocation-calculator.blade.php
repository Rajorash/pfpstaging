<div class="p-6 sm:px-20">
    <div class="mb-4 text-lg text-center">{{__('Allocation Calculator For')}} {{$this->business->name}}
        <select class="px-3 py-1 pr-8 ml-4 rounded form-select" name="" id="" wire:model="selectedBusinessId">
            @foreach ($selectOptions as $business_id => $business_name)
                <option value="{{$business_id}}">{{$business_name}}</option>
            @endforeach
        </select>
    </div>
    @if ($this->checkPercentagesSet() == false)
        <div class="p-4 text-red-600 bg-red-100 border border-red-700 rounded-sm">
            It appears that the percentage values have not been set yet for this phase. You may go to the <a class="underline transition duration-500 ease-in-out hover:text-red-900" href="{{route('allocations-percentages', ['business' => $this->business])}}">percentages page</a> to set them.
        </div>
    @endif
    <x-ui.table>
        <tr>
            <x-ui.th></x-ui.th>
            <x-ui.th></x-ui.th>
            <x-ui.th class="w-10">{{__('Actual')}}</x-ui.th>
            <x-ui.th>{{__('Roll Out %')}}</x-ui.th>
            <x-ui.th>{{__('Allocation $')}}</x-ui.th>
        </tr>
        {{-- Revenue account/s - should only && always be one? --}}
        @if (array_key_exists('revenue', $mappedAccounts))
            @foreach ($mappedAccounts['revenue'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{__('Top line revenue - Account')}} "{{$account['name']}}"</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="w-32 px-2 py-1 bg-yellow-100 border border-gray-300">
                    <x-ui.input type="text" wire:model="revenue"/>
                </td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
            </tr>
            @endforeach
        @endif
        {{-- Sales tax account/s --}}
        @if (!$this->hideSalesTaxRows())
            @foreach ($mappedAccounts['salestax'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 text-right bg-indigo-200 border border-gray-300">{{$account['percent']}}%</td>
                <td class="px-2 py-1 text-right bg-green-300 border border-gray-300">
                    ${{number_format($account['value'], 0)}}</td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
            </tr>
            @endforeach

            {{-- Net Cash Receipts --}}
            <tr class="">
                <td class="px-2 py-1 border border-gray-300">{{__('Net Cash Receipts')}}</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="px-2 py-1 text-right border border-gray-300">${{number_format($netCashReceipts, 0)}}</td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
            </tr>
        @endif

        {{-- Pre-real account/s --}}
        @if (array_key_exists('prereal', $mappedAccounts))
            @foreach ($mappedAccounts['prereal'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 text-right bg-indigo-200 border border-gray-300">{{$account['percent']}}%</td>
                <td class="px-2 py-1 text-right bg-green-100 border border-gray-300">
                    ${{number_format($account['value'], 0)}}</td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
                <td class="px-2 py-1 bg-gray-200 border border-gray-300"></td>
            </tr>
            @endforeach
        @endif

        {{-- Real Revenue --}}
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">{{__('Real Revenue')}}</td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 text-right bg-green-100 border border-gray-300">
                ${{number_format($realRevenue, 0)}}</td>
            <td class="text-right px-2 py-1 border border-gray-300 {!! ($postrealPercentageSum > 100 || $postrealPercentageSum < 100) ? 'text-red-500' : '';!!}">{{$postrealPercentageSum}}
                %
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
        </tr>

        {{-- Post-real accounts --}}
        @if (array_key_exists('postreal', $mappedAccounts))
            @foreach ($mappedAccounts['postreal'] as $account)
            <tr>
                <td class="px-2 py-1 border border-gray-300">{{$account['name']}}</td>
                <td class="px-2 py-1 border border-gray-300"></td>
                <td class="py-1 border border-gray-300 bg-green-100px-2"></td>
                <td class="px-2 py-1 text-right bg-gray-400 border border-gray-300">{{$account['percent']}}%</td>
                <td class="px-2 py-1 text-right bg-green-200 border border-gray-300">
                    ${{number_format($account['value'], 0)}}</td>
            </tr>
            @endforeach
        @endif

        {{-- Check sum --}}
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">
                {{__('Allocation Sum')}}
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 text-right border border-gray-300">
                ${{number_format($allocationSum, 2)}}
        </tr>
        <tr class="bg-gray-200">
            <td class="px-2 py-1 border border-gray-300">
                {{__('Error Check')}}
            </td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 border border-gray-300"></td>
            <td class="px-2 py-1 text-right border border-gray-300">
                <span
                    class="{!! round($checksum, 2) == 0 ? '' : 'text-red-500';!!}">${{number_format($checksum, 2)}}</span>
        </tr>
    </x-ui.table>
</div>
