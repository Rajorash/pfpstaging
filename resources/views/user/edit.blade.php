<x-app-layout>
    <x-slot name="header">
        Users
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Edit User
    </x-slot>


    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-72 relative">
                Edit User {{$user->name}}

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
                        <form method="POST" action="{{route('users.update', ['user'=>$user])}}">
                            @csrf
                            @method('PUT')
                            <div class="table w-full mt-10">

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Allow interaction?') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <div class="text-left my-2">
                                            <input type="checkbox" name="active" id="active"
                                                   value="1" {{ $user->active ? ' checked' : '' }} />
                                            <label for="active" class="pl-2">{{__('Yes, User can interaction with the site')}}</label>
                                            <div class="text-sm pt-2 text-gray-500">{{__('This item + licenses for advisor and client will determine whether the user will be active')}}</div>
                                        </div>
                                        <x-jet-input-error for="active" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Name') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="name" class=" w-full" type="text" name="name"
                                                     value="{{$user->name}}" required autofocus/>
                                        <x-jet-input-error for="name" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('E-Mail Address') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="email" class=" w-full" type="email" name="email"
                                                     value="{{$user->email}}" required autofocus/>
                                        <x-jet-input-error for="email" class="mt-2"/>
                                    </div>
                                </div>
                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Title') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="title" class=" w-full" type="text" name="title"
                                                     value="{{$user->title}}" autofocus/>
                                        <x-jet-input-error for="title" class="mt-2"/>
                                    </div>
                                </div>
                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Responsibility') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="responsibility" class=" w-full" type="text"
                                                     name="responsibility"
                                                     value="{{$user->responsibility}}" autofocus/>
                                        <x-jet-input-error for="responsibility" class="mt-2"/>
                                    </div>
                                </div>

                                @if(count($roles) > 1)
                                    <div class="table-row">
                                        <div class="table-cell w-1/4 pb-4 text-left align-top">
                                            {{ __('Roles:') }}
                                        </div>
                                        <div class="table-cell w-3/4 pb-4">
                                            @foreach ($roles as $role_id => $role_label)
                                                <div class="text-left my-2">
                                                    <input type="checkbox" name="roles[]" id="roles_{{$role_id}}"
                                                           value="{{ $role_id }}"{{ is_array($userRoles) && in_array($role_id, $userRoles) ? ' checked' : '' }} />
                                                    <label for="roles_{{$role_id}}">{{ $role_label }}</label>
                                                </div>
                                            @endforeach
                                            <x-jet-input-error for="roles" class="mt-2"/>
                                            @if($errors->has('roles'))
                                                <p class="text-sm text-red-600 mt-2">{{ $errors->first('roles') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="roles[0]" id="roles"
                                           value="{{array_key_first($userRoles)}}">
                                    <div class="table-row">
                                        <div class="table-cell w-1/4 pb-4 text-left align-top">
                                            {{ __('Role:') }}
                                        </div>
                                        <div class="table-cell w-3/4 pb-4 text-left">
                                            {{ array_shift($userRoleLabels) }}
                                        </div>
                                    </div>
                                @endif

                                @if (count($businesses) > 0)
                                    <div class="table-row">
                                        <div class="table-cell w-1/4 pb-4 text-left align-top">
                                            {{ __('Available for licensing:') }}
                                        </div>
                                        <div class="table-cell w-3/4 pb-4">
                                            @foreach ($businesses as $business_id => $business_name)
                                                <div class="text-left my-2">
                                                    <input type="checkbox" name="licenses[]"
                                                           id="licenses_{{$business_id}}"
                                                           value="{{ $business_id }}"{{ is_array($licenses) && in_array($business_id, $licenses) ? ' checked' : '' }} />
                                                    <label for="roles_{{$business_id}}">{{ $business_name }}</label>
                                                </div>
                                            @endforeach
                                            <x-jet-input-error for="roles" class="mt-2"/>
                                        </div>
                                    </div>
                                @endif

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Timezone:') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <select name="timezone" id="timezone"
                                                class="w-full form-input border-light_blue
                                                        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                        rounded-md shadow-sm">
                                            <option>Select your timezone</option>
                                            @foreach (timezone_identifiers_list(64) as $timezone)
                                                <option
                                                    value="{{ $timezone }}"{{ $timezone == $user->timezone ? ' selected' : '' }}>{{ $timezone }}</option>
                                            @endforeach
                                        </select>
                                        <x-jet-input-error for="timezone" class="mt-2"/>
                                    </div>
                                </div>

                            </div>

                            <div class="table w-full mt-4">
                                <div class="table-row">
                                    <div class="table-cell w-full pb-4 text-right">
                                        <x-ui.button-normal class="uppercase" type="button">
                                            Save User
                                        </x-ui.button-normal>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
