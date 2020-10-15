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
                        @for($i = 1; $i < 8; $i++)
                        <th class="text-center">Phase {{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                @forelse($business->accounts as $acc)
                    <tr>
                        <td scope="row">{{ $acc->name }}</td>
                        @for($i = 1; $i < 8; $i++)
                        <td class="text-center">0%</td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td scope="row">N/A</td>
                        @for($i = 1; $i < 8; $i++)
                        <td class="text-center">N/A</td>
                        @endfor
                    </tr>
                @endforelse
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</div>

@endsection