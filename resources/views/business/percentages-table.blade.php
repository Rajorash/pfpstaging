<x-ui.table-table class="cursor-fill-data relative">
    <thead>
    <tr class="border-b divide-x border-light_blue">
        <x-ui.table-th class="text-left sticky top-0 left-0 z-40"
                       baseClass="w-24 pr-2 pl-4 text-dark_gray font-normal bg-white">
            {{__('Account')}}
        </x-ui.table-th>
        @forelse($rollout as $phase)
            @php $isCurrent = $phase->id == $business->current_phase; @endphp
            <x-ui.table-th baseClass="w-24 font-normal"
                           class="text-center sticky top-0 z-30 left-0 min-w-24 {{ $isCurrent ? 'bg-blue text-white': 'bg-white text-dark_gray' }}">
                <span class="block text-xs font-normal mb-2" title="{{__('Start of phase')}}">
                    {{$phase->start_date->format('j, M Y')}}
                </span>
                <span class="block text-normal" title="{{__('End of phase')}}">
                    {{$phase->end_date->format('M Y')}}
                </span>
                <span class="block text-xl" title="{{__('End of phase')}}">
                    {{$phase->end_date->format('j')}}
                </span>
            </x-ui.table-th>
        @empty
            <x-ui.table-th class="text-center sticky top-0 z-10 min-w-20" baseClass="w-24 text-dark_gray font-normal">
                {{__('No phases exist...')}}
            </x-ui.table-th>
        @endforelse
    </tr>
    </thead>

    <x-ui.table-tbody>
        @php
            $account_order = ['revenue','pretotal','salestax','prereal','postreal'];
            $accounts = $business->accounts;
            $sorted = $accounts->sortBy( function($account) use ($account_order) {
                return array_search($account->type, $account_order);
            });

            $account_class = [
                'pretotal' => 'bg-red-100',
                'salestax' => 'bg-gray-100',
                'prereal' => 'bg-yellow-200',
                'postreal' => 'bg-indigo-100'
            ];

            $rowIndex = 1;
            $columnIndex = 0;

        @endphp

        @forelse($sorted as $acc)

            @php
                $columnIndex = 0;
            @endphp

            @if ($acc->type == "revenue")
                @continue
            @endif
            <tr class="{{$account_class[$acc->type]}} hover:bg-yellow-100 border-light_blue divide-x">
                <x-ui.table-td padding="p-1 pr-2 pl-4"
                               class="text-left sticky left-0 z-30 {{$account_class[$acc->type]}} text-{{strtolower($acc->type)}}">{{ $acc->name }}</x-ui.table-td>

                @forelse($rollout as $phase)
                    @php
                        $columnIndex++;
                    @endphp
                    @php
                        $percentage = $percentages
                            ->where('phase_id', '=', $phase->id)
                            ->where('bank_account_id', '=', $acc->id)
                            ->pluck('percent')
                            ->first()
                            ?? null
                    @endphp
                    <x-ui.table-td padding="p-0" class="text-right relative">
                        <input draggable="true" class="percentage-value {{$acc->type}}
                            pfp_copy_move_element
                            border-0 border-transparent bg-transparent
                            hover:bg-yellow-50 z-0
                            focus:outline-none focus:ring-1 focus:shadow-none focus:bg-yellow-50
                            m-0 outline-none postreal text-right w-full"
                               data-phase-id="{{$phase->id}}"
                               data-account-id="{{$acc->id}}"
                               data-row="{{$rowIndex}}"
                               data-column="{{$columnIndex}}"
                               placeholder="0"
                               autocomplete="off"
                               name="percentage"
                               type="text" pattern="[0-9]{10}"
                               id="{{$acc->type}}_{{$phase->id}}_{{$acc->id}}"
                               value="{{$percentage ?? number_format(0, 2, '.', ''); }}"
                               @if($currentUser && $business->license->checkLicense) @else disabled @endif
                        >
                    </x-ui.table-td>
                @empty
                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @endforelse
            </tr>
            @php
                $rowIndex++;
            @endphp
        @empty
            <tr class="bg-white divide-x border-light_blue">
                <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @forelse($rollout as $phase)
                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @empty

                @endforelse
            </tr>
        @endforelse
    </x-ui.table-tbody>
    <tfoot>
    <tr class="divide-x bg-blue border-light_blue">
        <x-ui.table-td padding="p-2" class="text-center" baseClass="text-white">
            <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
        </x-ui.table-td>
        @forelse($rollout as $phase)
            <x-ui.table-td padding="p-2 pr-4 pl-6"
                           class="text-right percentage-total {{ $phase->total != '100' ? 'bg-red-500' : '' }}"
                           baseClass="text-white">
                {{$phase->total}}%
            </x-ui.table-td>
        @empty
            <x-ui.table-td padding="p-2 pr-2 pl-6" class="text-center percentage-total" baseClass="text-white">
                {{__('No phases exist...')}}
            </x-ui.table-td>
        @endforelse
    </tr>
    </tfoot>
</x-ui.table-table>
<div style="display: none;" id="php_lastData" data-last_row_index="{{$rowIndex}}"
     data-last_row_index="{{$columnIndex}}"></div>
