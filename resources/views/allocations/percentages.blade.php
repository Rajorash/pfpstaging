<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Percentages
            </h2>
            <x-business-nav businessId="{{$business->id}}" />
            </div>

        </x-slot>

        <div class="w-5/6 py-3 mx-auto">
        <x-ui.table tableId=percentagesTable>
            <thead class="thead-inverse">
                <tr>
                    <th class="px-2">Account</th>
                    @forelse($rollout as $phase)
                    <th class="px-2 text-center">Phase Ends:<br> {{ Carbon\Carbon::parse($phase->end_date)->format("D j M") }}</th>
                    @empty
                    <th class="px-2 text-center">No phases exist...</th>
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
                        'pretotal' => 'bg-red-300',
                        'salestax' => 'bg-gray-300',
                        'prereal' => 'bg-yellow-300',
                        'postreal' => 'bg-blue
                    ];
                @endphp
                @forelse($sorted as $acc)
                @if ($acc->type == "revenue")
                @continue
                @endif
                <tr class="{{$account_class[$acc->type]}}">
                    <td scope="row" class="px-3">{{ $acc->name }}</td>
                    @forelse($rollout as $phase)
                    @php
                    $percentage = $percentages
                        ->where('phase_id', '=', $phase->id)
                        ->where('bank_account_id', '=', $acc->id)
                        ->pluck('percent')
                        ->first()
                        ?? null
                    @endphp

                    <td class="text-center w-30 p-1">
                        <input class="percentage-value {{$acc->type}} text-right w-full py-0 rounded"
                        data-phase-id={{$phase->id}}
                        data-account-id={{$acc->id}}
                        placeholder="0"
                        name="percentage"
                        type="text"
                        value="{{$percentage}}"
                        >
                    </td>
                    @empty
                    <td class="text-center">N/A</td>
                    @endforelse
                </tr>
                @empty
                <tr>
                    <td scope="row">N/A</td>
                    @forelse($rollout as $phase)
                    <td class="text-center">N/A</td>
                    @empty

                    @endforelse
                </tr>
                @endforelse
                <tr>
                    <td></td>
                    @forelse($rollout as $phase)
                    <td class="bg-blue percentage-total text-right" data-phase-id="{{ $phase->id }}" data-value="0">0%</td>
                    @empty
                    <th class="text-center">No phases exist...</th>
                    @endforelse
                </tr>
            </tbody>
        </x-ui.table>
        </div>
    </x-app-layout>
