<x-app-layout>
    <x-slot name="header">
        Users
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden sm:rounded-lg">
            <x-ui.card bodypadding="0">
                <x-slot name="header">
                    <h2 class="text-lg leading-6 font-medium text-black">Users Visible To You</h2>
                    @if( Auth::user()->roles->pluck('name')->contains('advisor') )
                        <a href="{{route('users.create')}}"
                           class="group flex items-center text-center select-none border font-normal whitespace-no-wrap
                           rounded py-1 px-3 leading-normal no-underline bg-green text-white hover:bg-dark_gray2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Create Client
                        </a>
                    @endif
                </x-slot>
                <x-ui.user-table :users="$users"/>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
