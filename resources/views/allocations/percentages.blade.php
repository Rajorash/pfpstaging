<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Percentages
            </h2>
            <x-business-nav businessId="{{$business->id}}" />
            </div>

        </x-slot>

    <div class="flex flex-wrap  items-center">
        <!-- <div class="relative flex-grow max-w-full flex-1 px-4"> -->
            <table class="w-5/6 mx-auto mb-4 bg-transparent block scrolling-touch table-hover p-3">
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
                @forelse($business->accounts as $acc)
                    @if ($acc->type == "revenue")
                        @continue
                    @endif
                    <tr>
                        <td scope="row">{{ $acc->name }}</td>
                        @forelse($rollout as $phase)

                        <td class="text-center w-30 p-1">
                            <input class="percentage-value text-right w-full"
                                data-phase-id={{$phase->id}}
                                data-account-id={{$acc->id}}
                                placeholder="0"
                                name="percentage"
                                type="text"
                                @php
                                $value = $percentageValues[$acc->id][$phase->id] ?? null
                                @endphp
                                value="{{$value}}"
                                >
                            {{-- {{Form::text('percentage', $percentageValues[$acc->id][$phase->id] ?? null, ['class' => 'percentage-value text-right form-control form-control-sm', 'data-phase-id' => $phase->id, 'data-account-id' => $acc->id, 'placeholder' => 0])}} --}}
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
                    <td class="percentage-total text-right" data-phase-id="{{ $phase->id }}" data-value="0">0%</td>
                    @empty
                    <th class="text-center">No phases exist...</th>
                    @endforelse
                </tr>
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</x-app-layout>
