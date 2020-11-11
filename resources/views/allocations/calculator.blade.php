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
                        <span style="{{$today->format('Y-m-j') == $date ? 'color: #bada55' : ''}}">{{ Carbon\Carbon::parse($date)->format("M j Y") }}</span>
                    </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr>
                        <td scope="row account-row" style="background-color:#99ccdd;">{{$acc->name}}</td>
                        @foreach($dates as $date)
                        <td class="text-right account"
                            style="background-color:#99ccdd;"
                            data-date='{{$date}}'
                            data-percentage='{{$allocationPercentages[1][$acc->id]}}'
                            {{-- @if($acc->type == 'salestax') --}}
                            {{-- data-tax={{$taxRates[$acc->id]}} --}}
                            {{-- @endif --}}
                            data-row='{{$loop->parent->iteration}}'
                            data-col='{{$loop->iteration}}'>
                            <input type="text"
                                   class="bg-info projected-total text-right form-control form-control-sm"
                                   data-hierarchy="{{$acc->type}}"
                                   data-date='{{$date}}'
                                   placeholder="0"
                                   disabled>
                            <input type="text"
                                   class="account-value text-right allocation-value form-control form-control-sm"
                                   style="min-width: 8em;"
                                   data-type="BankAccount"
                                   data-hierarchy="{{$acc->type}}"
                                   data-id="{{$acc->id}}"
                                   data-date="{{$date}}"
                                   value="{{$allocationValues['BankAccount'][$acc->id][$date] ?? 0}}"
                                   disabled>
                        </td>
                        @endforeach
                    </tr>
                    @forelse ($acc->flows as $flow)
                    <tr>
                        <td class="flow-label" style="background-color: {{ $flow->negative_flow ? '#dd9999' : '#99dd99' }}" scope="row">{{$flow->label}}</td>
                        @foreach($dates as $date)
                        <td class="text-right flow">
                            <input style="min-width: 8em;"
                                class="text-right allocation-value form-control form-control-sm"
                                data-type="AccountFlow"
                                data-id="{{$flow->id}}"
                                data-date="{{$date}}"
                                data-parent="{{$acc->id}}"
                                data-direction="{{$flow->negative_flow ? 'negative' : 'positive'}}"
                                placeholder='0'
                                type="text"
                                value="{{$allocationValues['AccountFlow'][$flow->id][$date] ?? ''}}">
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
