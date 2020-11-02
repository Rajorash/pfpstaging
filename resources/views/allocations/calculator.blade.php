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

                    @for( $date = $start_date; $date <= $end_date; $date->addDay(1) )
                    <th class="text-right">
                        <span style="{{$today->format('jM') == $date->format('jM') ? 'color: #bada55' : ''}}">{{ $date->format('M j') }}</span>
                    </th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr>
                        <td scope="row" style="background-color:#99ccdd;">{{$acc->name}}</td>
                        @for($i = 0; $i < 31; $i++)
                        <td class="text-right" style="background-color:#99ccdd;">
                            <input style="min-width: 8em;" class="text-right allocation-value form-control form-control-sm" data-type="BankAccount" data-id="{{$acc->id}}" type="text" value="0">
                        </td>
                        @endfor
                    </tr>
                    @forelse ($acc->flows as $flow)
                    <tr>
                        <td style="background-color: {{ $flow->negative_flow ? '#dd9999' : '#99dd99' }}" scope="row">{{$flow->label}}</td>
                        @for($i = 0; $i < 31; $i++)
                        <td class="text-right">
                            <input style="min-width: 8em;" class="text-right allocation-value form-control form-control-sm" data-type="AccountFlow" data-id="{{$flow->id}}" type="text" value="0">
                        </td>
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
