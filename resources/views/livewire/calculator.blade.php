<div class="">
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
        @php
        $types = ['revenue', 'pretotal', 'salestax', 'prereal', 'postreal'];
        @endphp


        <x-ui.table tableId=allocationTable>

            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">
                    </th>
                    {{-- @dd($dates) --}}
                    @foreach ($dates as $date)
                    <th scope="col" class="px-2 py-3 text-center font-sans font-semibold text-gray-500 tracking-wide">
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
                <x-calculator.account-row :acc="$acc" :dates="$dates" :first="$first_of_type" row="{{$account_row_index}}"  />

                @foreach ($acc->flows as $flow)
                {{-- START flow loop --}}
                <x-calculator.flow-row :flow="$flow" :dates="$dates" />
                {{-- END flow loop --}}
                @endforeach

                {{-- END account loop --}}
                @endforeach

                {{-- END account type loop --}}
                @endforeach
            </tbody>
        </x-ui.table>



    </div>
