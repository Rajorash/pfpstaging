<div class="">
    <div class="container mx-auto flex text-left font-medium text-gray-700">
        <div class="py-2 pr-6">
            {{-- <label classfor="">Start date</label> --}}
            {{-- <input name="startdate" type="date" value="" wire:model="dateInput"> --}}
        </div>
        <div class="py-2 pr-6">
            <label for="range">Range</label>
            <select name="range" id="range" wire:model="daysPerPage">
                <option class="form-input" value="7">Weekly</option>
                <option class="form-select" value="14">Fortnightly</option>
            </select>
        </div>
    </div>
    {{--
        revenue -
        pretotal =
        Receipts to be allocated -
        salestax =
        Net Cash Receipts -
        prereal =
        Real Revenue -
        postreal
        --}}
        <x-ui.table tableId=allocationTable>

            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                    </th>
                    {{-- @dd($dates) --}}
                    @foreach ($dates as $date)
                    @php
                        $date = Carbon\Carbon::parse($date);
                    @endphp
                    {{-- @dump($date) --}}
                    <th scope="col" class="px-2 py-3 text-center font-sans font-semibold {{ $date->isToday() ? 'text-green-500': 'text-gray-500' }} tracking-normal">
                        {{ $date->format('D - M j Y') }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $account_row_index = 0; @endphp
                @foreach ($types as $type)
                {{-- START account type loop --}}
                @foreach ($accounts[$type] as $acc)
                {{-- START account loop --}}
                @php
                    $account_row_index++;
                    $first_of_type = $loop->first;
                @endphp
                <livewire:calculator.account-row :acc="$acc" :dates="$dates" :first="$first_of_type" rowId="$account_row_index" :key="$acc->id">
                {{-- <x-calculator.account-row :acc="$acc" :dates="$dates" :first="$first_of_type" :type="$acc->type" row="{{$account_row_index}}" :key="$acc->id" /> --}}

                @foreach ($acc->flows as $flow)
                {{-- START flow loop --}}
                <x-calculator.flow-row :flow="$flow" :dates="$dates" key="$flow->id" />
                {{-- END flow loop --}}
                @endforeach

                {{-- END account loop --}}
                @endforeach

                {{-- END account type loop --}}
                @endforeach
            </tbody>
        </x-ui.table>



    </div>
