@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <!-- <div class="col"> -->
            <h1>{{$business->name}} Allocation Percentages</h1>
        <!-- </div> -->
    </div>
    <div class="row justify-content-center">
        <!-- <div class="col"> -->
            <table class="table table-hover table-sm">
                <thead class="thead-inverse">
                    <tr>
                        <th>Account</th>
                        @forelse($rollout as $phase)
                        <th class="text-center">Phase Ends:<br> {{date('D j M', strtotime($phase->end_date))}}</th>
                        @empty
                        <th class="text-center">No dice...</th>
                        
                        @endforelse
                    </tr>
                </thead>
                <tbody>
                @forelse($business->accounts as $acc)
                    <tr>
                        <td scope="row">{{ $acc->name }}</td>
                        @forelse($rollout as $phase)
                        <td class="text-center">0%</td>
                        @empty
                        <td class="text-center">N/A</td>
                        @endforelse
                    </tr>
                @empty
                    <tr>
                        <td scope="row">N/A</td>
                        @forelse($rollout as $phase)
                        <td class="text-center">N/A</td>
                        @empty                        @endforelse
                    </tr>
                @endforelse
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</div>

@endsection