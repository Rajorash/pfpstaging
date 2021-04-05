<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Percentages
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>


    <div class="rounded-xl">
        <table id="percentagesTable" cellpadding="0" cellspacing="0"
               class="border-collapse rounded-xl bg-white w-full text-dark_gray2">
            <thead>
            <tr>
                <th class="border border-gray-300 p-4 rounded-t-xl">
                    Account
                </th>

                @forelse($rollout as $phase)
                    <th class="border border-gray-300 p-4 {{ Carbon\Carbon::parse($phase->end_date)->isToday() ? 'text-blue': '' }} ">
                        {{--                        <span class="block text-xs">Phase Ends:</span>--}}
                        <span
                            class="block text-xs font-normal">{{Carbon\Carbon::parse($phase->end_date)->format('M Y')}}</span>
                        <span class="block text-xl">{{Carbon\Carbon::parse($phase->end_date)->format('j')}}</span>
                    </th>
                @empty
                    <th class="border border-gray-300 p-4">No phases exist...</th>
                @endforelse
            </tr>
            </thead>

            <tbody>
            @php
                $account_order = ['revenue','pretotal','salestax','prereal','postreal'];
                $accounts = $business->accounts;
                $sorted = $accounts->sortBy( function($account) use ($account_order) {
                    return array_search($account->type, $account_order);
                });

                $account_class = [
                    'pretotal' => 'bg-red-100',
                    'salestax' => 'bg-gray-100',
                    'prereal' => 'bg-yellow-100',
                    'postreal' => 'bg-indigo-100'
                ];
            @endphp
            @forelse($sorted as $acc)
                @if ($acc->type == "revenue")
                    @continue
                @endif
                <tr class="{{$account_class[$acc->type]}}">
                    <td scope="row" class="border border-gray-300 whitespace-nowrap p-1 pr-2 pl-4">{{ $acc->name }}</td>
                    @forelse($rollout as $phase)
                        @php
                            $percentage = $percentages
                                ->where('phase_id', '=', $phase->id)
                                ->where('bank_account_id', '=', $acc->id)
                                ->pluck('percent')
                                ->first()
                                ?? null
                        @endphp

                        <td class="border border-gray-300 text-right p-1">
                            <input class="percentage-value {{$acc->type}}
                                px-2 py-0 w-20 text-right bg-transparent border-0 border-b border-transparent outline-none
                                focus:border-yellow-700 focus:outline-none focus:shadow-none focus:ring-0
                                "
                                   data-phase-id={{$phase->id}}
                                       data-account-id={{$acc->id}}
                                       placeholder="0"
                                   name="percentage"
                                   type="text"
                                   value="{{$percentage}}"
                            >
                        </td>
                    @empty
                        <td class="text-center border border-gray-300 p-1 pr-2 pl-6">N/A</td>
                    @endforelse
                </tr>
            @empty
                <tr>
                    <td scope="row">N/A</td>
                    @forelse($rollout as $phase)
                        <td class="text-center border border-gray-300 p-1 pr-2 pl-6">N/A</td>
                    @empty

                    @endforelse
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr>
                <td class="bg-transparent"></td>
                @forelse($rollout as $phase)
                    <td class="border bg-indigo-200 border-gray-300 p-1 pr-2 pl-6 percentage-total text-right" data-phase-id="{{ $phase->id }}" data-value="0">
                        0%
                    </td>
                @empty
                    <td class="text-center">No phases exist...</td>
                @endforelse
            </tr>
            </tfoot>
        </table>
    </div>
</x-app-layout>
