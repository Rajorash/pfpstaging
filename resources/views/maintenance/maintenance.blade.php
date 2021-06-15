<x-app-layout>
    <x-slot name="header">
        {{ __('Maintenance') }}
    </x-slot>

    <x-ui.main>
        <livewire:maintenance :code="$code" />
    </x-ui.main>

</x-app-layout>
