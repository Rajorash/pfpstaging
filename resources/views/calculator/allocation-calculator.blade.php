<x-app-layout>
    <x-slot name="header">
        {{ __('Allocations Calculator') }}
    </x-slot>

    @if($business ?? false)
    <x-slot name="subMenu">
        <x-business-nav businessId="{{$business->id}}" :business="$business"/>
    </x-slot>
    @endif

    <x-ui.main>
        <livewire:allocation-calculator :businesses="$businesses" :businessId="$businessId"/>
    </x-ui.main>

</x-app-layout>
