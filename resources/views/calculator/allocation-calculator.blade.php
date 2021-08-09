<x-app-layout>
    <x-slot name="header">
        {{ __('Allocations Calculator') }}
    </x-slot>

    <x-ui.main>
        <livewire:allocation-calculator :businesses="$businesses" :businessId="$businessId"/>
    </x-ui.main>

</x-app-layout>
