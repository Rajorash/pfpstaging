@extends('layouts.app')

@section('content')
<x-business-nav businessId="{{$business->id}}" />
<div class="container mx-auto sm:px-4 max-w-full mx-auto sm:px-4">
    <div class="flex flex-wrap  justify-center">
        <h1>{{$business->name}} Projections</h1>
    </div>
    <div class="flex flex-wrap  px-2 justify-center">
        <table id="projectionTable" class="w-full max-w-full mb-4 bg-transparent table-hover p-1">
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
