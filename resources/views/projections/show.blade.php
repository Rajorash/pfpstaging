@extends('layouts.app')

@section('content')
<div class="container secondary-nav nav mb-3">
    <a class="ml-auto mr-2" href="/allocations/{{$business->id}}\percentages">See Percentages</a> |
    <a class="ml-2" href="/business/{{$business->id}}\accounts">See Accounts</a>
</div>
<div class="container-fluid">
    <div class="row justify-content-center px-5">
        <h1>{{$business->name}} Allocations</h1>
    </div>
    <div class="row">
        <table id="projectionTable" class="table table-hover table-sm table-responsive">
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
                @foreach($allocations as $allocation)
                <x-projection.allocation-row :dates="$dates" :allocation="$allocation"/>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
