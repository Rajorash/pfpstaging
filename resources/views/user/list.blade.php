<x-app-layout>
    <x-slot name="header">
        {{ __('Users') }}
    </x-slot>

    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption>
                <span>Users Visible To You</span>
                @if(
                    Auth::user()->isSuperAdmin() ||
                    Auth::user()->roles->pluck('name')->contains('admin') ||
                    Auth::user()->roles->pluck('name')->contains('advisor')
                    )
                    <x-slot name="right">
                        <x-ui.button-normal href="{{route('users.create')}}">
                            <x-icons.user-add/>
                            <span class="ml-2">Create User</span>
                        </x-ui.button-normal>
                    </x-slot>
                @endif
            </x-ui.table-caption>
            <thead>
            <tr class="border-light_blue border-t border-b">
                <x-ui.table-th padding="pl-12 pr-2 py-4">Name</x-ui.table-th>
                <x-ui.table-th>Title</x-ui.table-th>
                <x-ui.table-th class="text-center">Status</x-ui.table-th>
                <x-ui.table-th>Roles</x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                <x-ui.table-th></x-ui.table-th>
                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor())
                    <x-ui.table-th></x-ui.table-th>
                @endif
            </tr>
            </thead>

            <x-ui.table-tbody>
                @foreach ($users as $user)
                    <tr>
                        <x-ui.table-td class="whitespace-nowrap"
                                       padding="pl-12 pr-2 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <div class="text-sm text-dark_gray2">Regional Paradigm Technician</div>
                            <div class="text-sm text-light_gray">Optimization</div>
                        </x-ui.table-td>
                        <x-ui.table-td class="text-center">
                            @if($user->active)
                                <x-ui.badge>Active</x-ui.badge>
                            @else
                                <x-ui.badge background="bg-light_gray">Inactive</x-ui.badge>
                            @endif
                        </x-ui.table-td>
                        <x-ui.table-td>
                            {{ ucfirst( $user->roles->implode('name', ', ') ) }}
                        </x-ui.table-td>
                        <x-ui.table-td>
                            <x-ui.button-small href="{{url('/user/'.$user->id)}}">
                                See User
                            </x-ui.button-small>
                        </x-ui.table-td>
                        <x-ui.table-td>
                            @if($user->id != $currUserId)
                                <x-ui.button-small href="{{route('users.edit', ['user'=>$user])}}">
                                    Edit User
                                </x-ui.button-small>
                            @endif
                        </x-ui.table-td>
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor())
                            <x-ui.table-td>
                                <x-ui.button-small href="{{route('licenses.list', ['user'=>$user])}}">
                                    Licenses
                                </x-ui.button-small>
                            </x-ui.table-td>
                        @endif
                    </tr>
                @endforeach

            </x-ui.table-tbody>

        </x-ui.table-table>
        <div class="m-6">
            {{ $users->links() }}
        </div>
    </x-ui.main>

</x-app-layout>
