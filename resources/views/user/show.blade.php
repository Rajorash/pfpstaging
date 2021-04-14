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
                        <div class="table w-full">
                            <div class="table-row">
                                <div class="table-cell w-1/3 text-left">
                                    <img class="h-36 w-36 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
                                </div>
                                <div class="table-cell w-2/3 text-left">
                                    <div class="table w-full float-left">
                                        <div class="table-row">
                                            <div class="table-cell pb-2 w-1/3">{{__('Name')}}</div>
                                            <div class="table-cell pb-2 w-2/3">{{ $user->name }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Role/s')}}</div>
                                            <div class="table-cell pb-2">{{ implode(', ',$user->roles->pluck('label')->toArray()) }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Email Adress')}}</div>
                                            <div class="table-cell pb-2">{{ $user->email }}</div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell pb-2">{{__('Last Login')}}</div>
                                            <div class="table-cell pb-2">{{ $user->last_login_at ?: 'Unknown' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
