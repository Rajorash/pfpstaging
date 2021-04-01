<x-app-layout>
    <x-slot name="header">
        <h2 class="font-normal text-4xl text-dark_gray2 leading-tight mt-8">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-light_blue rounded-2xl">
                <x-home.welcome />
            </div>
        </div>
    </div>
</x-app-layout>
