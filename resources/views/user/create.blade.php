<x-app-layout>
    <x-slot name="header">
        Users
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        Create New
    </x-slot>


    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-72 relative">
                Create a New Client User

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
                        <form method="POST" action="{{route('users')}}">
                            @csrf

                            <div class="table w-full mt-10">

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Name') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="name" class=" w-full" type="text" name="name"
                                                     :value="old('name')" required autofocus/>
                                        <x-jet-input-error for="name" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('E-Mail Address') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="email" class=" w-full" type="email" name="name"
                                                     :value="old('email')" required autofocus/>
                                        <x-jet-input-error for="email" class="mt-2"/>
                                    </div>
                                </div>

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
                                                    value="{{ $timezone }}"{{ $timezone == old('timezone') ? ' selected' : '' }}>{{ $timezone }}</option>
                                            @endforeach
                                        </select>
                                        <x-jet-input-error for="timezone" class="mt-2"/>
                                    </div>
                                </div>

                                <div class="table-row">
                                    <div class="table-cell w-1/4 pb-4 text-left">
                                        {{ __('Business Name') }}
                                    </div>
                                    <div class="table-cell w-3/4 pb-4">
                                        <x-jet-input id="business_name" class=" w-full" type="text" name="business_name"
                                                     :value="old('business_name')" required autofocus/>
                                        <x-jet-input-error for="business_name" class="mt-2"/>
                                    </div>
                                </div>


                            </div>

                            <div class="table w-full mt-4">
                                <div class="table-row">
                                    <div class="table-cell w-full pb-4 text-right">
                                        <x-ui.button-normal class="uppercase" type="button">
                                            Create User
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
