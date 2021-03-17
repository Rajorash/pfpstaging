<div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    <div class="text-lg font-semibold text-center">Allocation Calculator for {{$this->business->name}}
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
        <tr>
            <td class="px-3 py-1">Top line revenue - Account "REVENUE"</td>
            <td class="px-3 py-1 w-1/6"></td>
            <td class="px-3 py-1 bg-yellow-300 w-1/6"><x-ui.input type="text" value="$11000" /></td>
            <td class="bg-black w-1/6"></td>
            <td class="bg-black w-1/6"></td>
        </tr>
        {{-- Sales tax account/s --}}
        <tr>
            <td class="px-3 py-1">Sales tax</td>
            <td class="px-3 py-1 bg-blue-300"><x-ui.input type="text" /></td>
            <td class="text-right px-3 py-1 bg-green-300">$1000</td>
            <td class="bg-black"></td>
            <td class="bg-black"></td>
        </tr>
        <tr>
            <td class="px-3 py-1">Net Cash Receipts</td>
            <td class="px-3 py-1"></td>
            <td class="text-right px-3 py-1">$10000</td>
            <td class="bg-black"></td>
            <td class="bg-black"></td>
        </tr>
        {{-- Pre-real account/s --}}
        <tr>
            <td class="px-3 py-1">Mats & Subs</td>
            <td class="px-3 py-1 bg-blue-300"><x-ui.input type="text" /></td>
            <td class="text-right px-3 py-1 bg-green-300">$2000</td>
            <td class="bg-black"></td>
            <td class="bg-black"></td>
        </tr>
        {{-- Net Cash Receipts --}}
        <tr>
            <td class="px-3 py-1">Net Cash Receipts</td>
            <td class="px-3 py-1"></td>
            <td class="text-right px-3 py-1 bg-green-100">$8000</td>
            <td class="text-right px-3 py-1">100%</td>
            <td class="bg-black"></td>
        </tr>
        {{-- Post-real accounts --}}
        @foreach ($mappedAccounts['postreal'] as $account)
        <tr>
            <td class="px-3 py-1">{{$account['name']}}</td>
            <td class="px-3 py-1"></td>
            <td class="bg-green-100"></td>
            <td class="text-right px-3 py-1 bg-blue-300"><x-ui.input value="10%" /></td>
            <td class="text-right px-3 py-1 bg-green-300">Amount</td>
        </tr>
        @endforeach
        {{-- Check sum --}}
        <tr class="bg-gray-100">
            <td class="px-3 py-1">Allocation Sum</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right px-3 py-1">$11000</td>
        </tr>


    </x-ui.table>
</div>
