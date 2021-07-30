<x-ui.table-table class="table-sticky-header table-sticky-column">
    <thead>
    <tr class="border-light_blue divide-x border-b">
        <x-ui.table-th class="text-center" baseClass="w-24 text-dark_gray font-normal bg-white">
            Account
        </x-ui.table-th>

        @forelse($rollout as $phase)
            <x-ui.table-th baseClass="w-24 font-normal bg-white"
                           class="text-center min-w-24 {{ Carbon\Carbon::parse($phase->end_date)->isToday() ? 'text-blue': 'text-dark_gray' }} ">
                <span
                    class="block text-xs font-normal">{{Carbon\Carbon::parse($phase->end_date)->format('M Y')}}</span>
                <span class="block text-xl">{{Carbon\Carbon::parse($phase->end_date)->format('j')}}</span>
            </x-ui.table-th>
        @empty
            <x-ui.table-th class="text-center min-w-20" baseClass="w-24 text-dark_gray font-normal">No phases exist...
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
        @endphp
        @forelse($sorted as $acc)
            @if ($acc->type == "revenue")
                @continue
            @endif
            <tr class="{{$account_class[$acc->type]}} hover:bg-yellow-100 border-light_blue divide-x">
                <x-ui.table-td padding="p-1 pr-2 pl-4" class="text-left sticky-column text-{{strtolower($acc->type)}}">{{ $acc->name }}</x-ui.table-td>

                @forelse($rollout as $phase)
                    @php
                        $percentage = $percentages
                            ->where('phase_id', '=', $phase->id)
                            ->where('bank_account_id', '=', $acc->id)
                            ->pluck('percent')
                            ->first()
                            ?? null
                    @endphp
                    <x-ui.table-td padding="p-0" class="text-right">
                        <input class="percentage-value {{$acc->type}}
                            border-0 border-transparent bg-transparent
                            focus:outline-none focus:ring-1 focus:shadow-none focus:bg-white
                            m-0 outline-none postreal text-right w-full"
                               data-phase-id="{{$phase->id}}"
                               data-account-id="{{$acc->id}}"
                               placeholder="0"
                               name="percentage"
                               type="text"
                               id="{{$acc->type}}_{{$phase->id}}_{{$acc->id}}"
                               value="{{$percentage}}"
                               @if(!$business->license->checkLicense) disabled @endif
                        >
                    </x-ui.table-td>
                @empty
                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @endforelse
            </tr>
        @empty
            <tr class="border-light_blue divide-x bg-white">
                <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @forelse($rollout as $phase)
                    <x-ui.table-td padding="p-1 pr-2 pl-6" class="text-center">N/A</x-ui.table-td>
                @empty

                @endforelse
            </tr>
        @endforelse
    </x-ui.table-tbody>
    <tfoot>
    <tr class="bg-blue border-light_blue divide-x">
        <x-ui.table-td padding="p-2" class="text-center" baseClass="text-white">
            <span id="processCounter" class="hidden opacity-50 font-normal text-xs"></span>
        </x-ui.table-td>
        @forelse($rollout as $phase)
            <x-ui.table-td padding="p-2 pr-2 pl-6"
                           class="text-right percentage-total {{ $phase->total != '100' ? 'bg-red-500' : '' }}"
                           baseClass="text-white">
                {{$phase->total}}%
            </x-ui.table-td>
        @empty
            <x-ui.table-td padding="p-2 pr-2 pl-6" class="text-center percentage-total" baseClass="text-white">
                No phases exist...
            </x-ui.table-td>
        @endforelse
    </tr>
    </tfoot>
</x-ui.table-table>
