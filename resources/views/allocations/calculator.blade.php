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
                    @foreach($dates as $date)
                    <th class="text-right">
                        <span style="{{$today->format('Y-m-j') == $date ? 'color: #bada55' : ''}}">{{ $date }}</span>
                    </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr>
                        <td scope="row" style="background-color:#99ccdd;">{{$acc->name}}</td>
                        @foreach($dates as $date)
                        <td class="text-right" style="background-color:#99ccdd;">
                            <input style="min-width: 8em;" class="text-right allocation-value form-control form-control-sm" data-type="BankAccount" data-id="{{$acc->id}}" data-date="{{$date}}" type="text" value="0">
                        </td>
                        @endforeach
                    </tr>
                    @forelse ($acc->flows as $flow)
                    <tr>
                        <td style="background-color: {{ $flow->negative_flow ? '#dd9999' : '#99dd99' }}" scope="row">{{$flow->label}}</td>
                        @foreach($dates as $date)
                        <td class="text-right">
                            <input style="min-width: 8em;" class="text-right allocation-value form-control form-control-sm" data-type="AccountFlow" data-id="{{$flow->id}}" data-date="{{$date}}" type="text" value="0">
                        </td>
                        @endforeach
                    </tr>
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
