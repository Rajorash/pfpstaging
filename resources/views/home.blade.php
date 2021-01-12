<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="relative flex flex-col min-w-0 rounded break-words border bg-white border-1 border-gray-300">
                    <div class="py-3 px-6 mb-0 bg-gray-200 border-b-1 border-gray-300 text-gray-900">Dashboard</div>

                    <div class="flex-auto p-6">
                        @if (session('status'))
                            <div class="relative px-3 py-3 mb-4 border rounded bg-green-200 border-green-300 text-green-800" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p>
                        You are logged in!
                        </p>

                        <strong>Roles of current user</strong>
                        <ul>
                            @foreach (Auth::user()->roles as $role)
                            <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>


                        <strong>Permissions for current user</strong>
                        <ul>
                            @foreach (Auth::user()->permissions() as $permission)
                            <li>{{ $permission }}</li>
                            @endforeach
                        </ul>

                        @can('see_clients')
                        <strong>Only users who can see clients can see this sentence.</strong>
                        @endcan
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
