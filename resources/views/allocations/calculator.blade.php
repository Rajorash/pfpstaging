@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <h1>{{$business->name}} Allocations</h1>
    </div>
    <div class="row">
        <table class="table table-hover table-sm table-responsive">
            <thead class="thead-inverse">
                <tr>
                    <th></th>
                    @for($i = 1; $i < 32; $i++)
                    <th>Jan {{$i}}</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr style="border-top: 2px solid #99ccdd;">
                        <td scope="row" style="background-color:#99ccdd;">{{$acc->name}}</td>
                        @for($i = 1; $i < 32; $i++)
                        <td>0</td>
                        @endfor
                    </tr>
                    @forelse ($acc->flows as $flow)
                    <tr>
                        <td style="background-color: {{$flow->negative_flow ? '#dd9999' : '#99dd99'}}" scope="row">{{$flow->label}}</td>
                        @for($i = 1; $i < 32; $i++)
                        <td>0</td>
                        @endfor                    </tr>
                    @empty
                    @endforelse
                    @empty
                    <tr>
                        <td scope="row">N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                    </tr>
                    @endforelse
                </tbody>
        </table>
    </div>
</div>

@endsection