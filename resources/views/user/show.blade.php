<x-app-layout>
    <x-slot name="header">
        Users
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Detail
    </x-slot>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg">

            <div
                class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-dark_gray flex items-center justify-between">
                <h2>This User Is</h2>
            </div>

            <div class="flex-auto p-6">
                <strong class="text-dark_gray2">{{ $user->name }}</strong><br>
                Email: {{ $user->email }}<br>
                Last login: {{ $user->last_login_at ?: 'Unknown' }}<br>
                <br>
                <a class="text-blue hover:text-dark_gray2" href="{{ route('users') }}">Back to User list</a>
            </div>

        </div>
    </div>


</x-app-layout>
