@extends('layouts.app')

@section('content')
<x-business-nav businessId="{{$business->id}}" />
<div class="container mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
            <h1>{{$business->name}} Allocation Percentages</h1>
    </div>
    <div class="flex flex-wrap  justify-center">
        <!-- <div class="relative flex-grow max-w-full flex-1 px-4"> -->
            <table class="w-full max-w-full mb-4 bg-transparent block w-full overflow-auto scrolling-touch table-hover p-1">
                <thead class="thead-inverse">
                    <tr>
                        <th>Account</th>
                        @forelse($rollout as $phase)
                        <th class="text-center">Phase Ends:<br> {{ Carbon\Carbon::parse($phase->end_date)->format("D j M") }}</th>
                        @empty
                        <th class="text-center">No phases exist...</th>
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

                        <td class="text-center">
                            {{Form::text('percentage', $percentageValues[$acc->id][$phase->id] ?? null, ['class' => 'percentage-value text-right form-control form-control-sm', 'data-phase-id' => $phase->id, 'data-account-id' => $acc->id, 'placeholder' => 0])}}
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
</div>

@endsection
