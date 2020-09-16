@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>Allocation Percentages</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Account</th>
                        @for($i = 1; $i < 8; $i++)
                        <th class="text-center">Phase {{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                @forelse($accounts as $acc)
                    <tr>
                        <td scope="row">{{ $acc['label'] }}</td>
                        @for($i = 1; $i < 8; $i++)
                        <td class="text-center">{{ $acc['percentage'] }}%</td>
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
        </div>
    </div>
</div>

@endsection