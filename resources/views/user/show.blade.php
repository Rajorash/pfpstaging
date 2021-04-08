<x-app-layout>
    <x-slot name="header">
        Users
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        User Details
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-72 relative">
                User Details

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal href="{{route('users')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">Go back</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

            </x-ui.table-caption>
            <x-ui.table-tbody>
                <tr>
                    <x-ui.table-td class="text-center bg-gray-100" padding="px-72 py-4">
                        <div class="flex-auto p-6">
                            <strong class="text-dark_gray2">{{ $user->name }}</strong><br>
                            Email: {{ $user->email }}<br>
                            Last login: {{ $user->last_login_at ?: 'Unknown' }}<br>
                        </div>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
