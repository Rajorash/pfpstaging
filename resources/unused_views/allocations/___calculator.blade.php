<x-app-layout>
    <x-slot name="header">
        <div class="flex content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{$business->name}} > Allocations
            </h2>
            <x-business-nav businessId="{{$business->id}}" :business="$business" />
        </div>
    </x-slot>

    <div class="py-1"></div>
    {{-- <livewire:calculator :business="$business" /> --}}
    {{-- <div class="py-3"></div> --}}
    @include('components.calculator.sheet')
</x-app-layout>
