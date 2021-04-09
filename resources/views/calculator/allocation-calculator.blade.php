<x-app-layout>
    <x-slot name="header">
        {{ __('Allocations Calculator') }}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg border border-gray-300">
            <livewire:allocation-calculator :businesses="$businesses"/>
        </div>
    </div>

</x-app-layout>
