<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Businesses') }}
        </h2>
    </x-slot>

    <x-ui.card>

        <x-slot name="header">
            <h2 class="text-lg leading-6 font-medium text-black">Select A Business To See It's Allocations</h2>
        </x-slot>

        <x-business.table-list :businesses="$businesses" />

    </x-ui.card>

</x-app-layout>
