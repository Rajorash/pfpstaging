<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Users > Detail
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900 flex items-center justify-between">

                    <h2>This User Is</h2>
                </div>

                <div class="flex-auto p-6">
                    <strong>{{ $user->name }}</strong><br>
                    Email: {{ $user->email }}<br>
                    Last login: {{ $user->last_login_at ?: 'Unknown' }}<br>
                    <br>
                    <a class="text-blue hover:text-dark_gray2" href="{{ route('users') }}">Back to User list</a>
                </div>

            </div>
        </div>
    </div>


</x-layout-app>
