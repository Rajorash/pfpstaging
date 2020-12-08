@extends('layouts.app')

@section('content')
<div class="container secondary-nav nav mb-3">
    <div class="ml-auto"><a class="ml-auto" href="/allocations/{{$business->id}}">See Allocations</a>   </div>
</div>
<div class="container">
    <div class="row justify-content-center">
            <h1>{{$business->name}} Allocation Percentages</h1>
    </div>
    <div class="row justify-content-center">
        <!-- <div class="col"> -->
            <table class="table table-responsive table-hover table-sm">
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
