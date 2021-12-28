<x-app-layout>

    <x-slot name="titleHeader">
        {{ __('Dashboard') }}
    </x-slot>

    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <x-ui.main>
        <x-home.welcome/>
    </x-ui.main>

</x-app-layout>
