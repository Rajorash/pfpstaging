<x-app-layout>
    <x-slot name="header">
        {{ __('Licenses of').' '.$user->name }}
    </x-slot>

    <x-ui.main>
        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-12 sm:px-24 md:px-36 lg:px-48 xl:px-60 2xl:px-72 relative">
                {{$user->name}}

                <x-slot name="left">
                    <div class="absolute left-12 top-12">
                        <x-ui.button-normal href="{{route('users')}}">
                            <x-icons.chevron-left :class="'h-3 w-auto'"/>
                            <span class="ml-2">{{__('Go back')}}</span>
                        </x-ui.button-normal>
                    </div>
                </x-slot>

            </x-ui.table-caption>
            <x-ui.table-tbody>

                <tr>
                    <x-ui.table-td class="text-center bg-gray-100"
                                   padding="px-12 sm:px-24 md:px-36 lg:px-48 xl:px-60 2xl:px-72 py-4">
                        <livewire:licenses-for-advisors :user="$user"/>
                    </x-ui.table-td>
            </x-ui.table-tbody>
        </x-ui.table-table>
    </x-ui.main>

</x-app-layout>
