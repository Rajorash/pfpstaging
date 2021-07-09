<div class="livewire-wrapper">

    @if(!$user && Auth::user()->isAdvisor())
    <div class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-left text-red-700">
        Please note: You will not be able to see a newly created client until they have a business licensed to you created. You will be redirected to the business view to create a business once you have finished creating a client user.
    </div>
    @endif

    <form wire:submit.prevent="store">
        <div class="table w-full mt-10">


            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Name') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="name" class=" w-full" type="text" name="name"
                                 required autofocus wire:model.lazy="name"/>
                    <x-jet-input-error for="name" class="mt-2 text-left"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('E-Mail Address') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="email" class=" w-full" type="email" name="email"
                                 required wire:model.lazy="email"/>
                    <x-jet-input-error for="email" class="mt-2"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Title') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="title" class=" w-full" type="text" name="title"
                                 required autofocus wire:model.lazy="title"/>
                    <x-jet-input-error for="title" class="mt-2"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Responsibility') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="responsibility" class=" w-full" type="text" name="responsibility"
                                 required autofocus wire:model.lazy="responsibility"/>
                    <x-jet-input-error for="responsibility" class="mt-2"/>
                </div>
            </div>

            @if(count($rolesArray))

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Roles:') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        @foreach ($rolesArray as $role_id => $role_label)
                            <div class="text-left my-2">
                                <input type="checkbox" name="roles[]" id="roles_{{$role_id}}"
                                       class="disabled:opacity-40"
                                       wire:model="roles.{{$role_id}}"

                                       @if(count($rolesArray) == 1)
                                       readonly
                                       @endif

                                       @if ($roleAdvisorId == $role_id && count($licenses))
                                       disabled
                                       @endif

                                       value="{{ $role_id }}"/>
                                <label for="roles_{{$role_id}}">{{ $role_label }}</label>
                                @if ($roleAdvisorId == $role_id && count($licenses))
                                    <p class="text-sm pl-5 italic">{{__('Advisor role can not be revoked if at least one business is selected for licensing.')}}</p>
                                @endif
                            </div>
                        @endforeach
                        <x-jet-input-error for="roles" class="mt-2"/>
                    </div>
                </div>

                @if(Auth::user()->isSuperAdmin() && in_array($roleAdvisorId, $roles))
                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Regional Admin:') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4">
                            <select name="" id="" wire:model="selectedAdminId"
                                    class="w-full form-input border-light_blue
                                                        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                        rounded-md shadow-sm">
                                <option>Select Regional Admin for Advisor</option>
                                @foreach ($adminsUsersArray as $admin_row)
                                    <option value="{{$admin_row->id}}">{{$admin_row->name}} ({{$admin_row->email}})
                                    </option>
                                @endforeach
                            </select>

                            <x-jet-input-error for="selectedAdminId" class="mt-2"/>
                        </div>
                    </div>
                @endif

            @endif

            @if(count($businesses))
                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left align-top">
                        {{ __('Available for licensing:') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        @foreach ($businesses as $business_id => $business_name)
                            <div class="text-left my-2">
                                <input type="checkbox" name="licenses[]"
                                       id="licenses_{{$business_id}}"
                                       wire:model="licenses.{{$business_id}}"
                                       value="{{ $business_id }}"/>
                                <label for="licenses_{{$business_id}}">{{ $business_name }}</label>
                            </div>
                        @endforeach
                        <x-jet-input-error for="licenses" class="mt-2"/>
                    </div>
                </div>
            @endif


            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Timezone:') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <select name="timezone" id="timezone" wire:model.lazy="timezone"
                            class="w-full form-input border-light_blue
                                                        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                                        rounded-md shadow-sm">
                        <option>Select your timezone</option>
                        @foreach (timezone_identifiers_list(64) as $timezone)
                            <option value="{{ $timezone }}">{{ $timezone }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="timezone" class="mt-2"/>
                </div>
            </div>
        </div>

        <div class="table w-full mt-4">
            <div class="table-row">
                <div class="table-cell w-full pb-4 text-right">
                    <x-ui.button-normal class="uppercase" type="button" wire:loading.attr="disabled">
                        @if($user)
                            Save User
                        @else
                            Create User
                        @endif
                    </x-ui.button-normal>
                </div>
            </div>
        </div>
    </form>
</div>
