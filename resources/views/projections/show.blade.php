<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Projections
            </h2>

            <x-business-nav businessId="{{$business->id}}" />

        </div>

    </x-slot>

    <div class="flex flex-wrap py-3 px-2 justify-center">
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
</x-app-layout>
