<x-app-layout>
    <x-slot name="header">
        {{$business->name}}
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Projections
    </x-slot>

    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>

    <div class="flex flex-wrap py-3 px-2 justify-center">
        <table id="projectionTable" class="w-full max-w-full mb-4 bg-transparent table-hover p-1">
            <thead class="thead-inverse">
            <tr>
                <th></th>
                @foreach($dates as $date)
                    <th class="text-center">
                        <span
                            class="{{$today->format('Y-m-j') == $date ? 'text-green-500' : ''}}">{{ Carbon\Carbon::parse($date)->format("M j Y") }}</span>
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
