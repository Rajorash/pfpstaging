<x-app-layout>

    <x-slot name="titleHeader">
        @if (Auth::user()->isRegionalAdmin()
             && !Auth::user()->isSuperAdmin()
             && !Auth::user()->isAdvisor()
             && !Auth::user()->isClient())
            {{ __('Advisors') }}
        @else
            {{ __('Users') }}
        @endif
        &gt;
        {{__('Create New')}}
    </x-slot>

    <x-slot name="header">
        @if (Auth::user()->isRegionalAdmin()
             && !Auth::user()->isSuperAdmin()
             && !Auth::user()->isAdvisor()
             && !Auth::user()->isClient())
            {{ __('Advisors') }}
        @else
            {{ __('Users') }}
        @endif
        <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'"/>
        {{__('Create New')}}
    </x-slot>


    <x-ui.main>

        <x-ui.table-table>
            <x-ui.table-caption class="pt-12 pb-6 px-48 lg:px-52 xl:px-60 2xl:px-72 relative relative">
                @if (Auth::user()->isRegionalAdmin()
                     && !Auth::user()->isSuperAdmin()
                     && !Auth::user()->isAdvisor()
                     && !Auth::user()->isClient())
                    {{__('Create a New Advisor')}}
                @else
                    {{__('Create a New User')}}
                @endif

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
                        <livewire:create-edit-user/>
                    </x-ui.table-td>
                </tr>
            </x-ui.table-tbody>
        </x-ui.table-table>

    </x-ui.main>

</x-app-layout>
