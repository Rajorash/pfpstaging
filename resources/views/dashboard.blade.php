<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-light_blue rounded-2xl">
                <x-home.welcome/>
            </div>
        </div>
    </div>
</x-app-layout>
