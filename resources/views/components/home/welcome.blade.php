<div class="p-6 sm:px-20 bg-white border-b border-light_blue">
    <div class="mt-8 text-blue">
        <x-icons.two-users :class="'w-8 h-auto'"/>
    </div>

    <div class="mt-4 text-2xl mt-2 text-blue">
        {{__('Welcome to the PF Prophet!')}}
    </div>

    <div class="mt-2 mb-2">
        {{__('This is a predictive financial tool to help you proactively plan your businesses financial needs.')}}
    </div>
</div>

<div class="bg-dashboard grid grid-cols-1
    @if(Auth::user()->isRegionalAdmin()
        || Auth::user()->isSuperAdmin()
        || Auth::user()->isAdvisor())
    md:grid-cols-2
    @else
    md:grid-cols-3
    @endif
    ">
    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdvisor() || Auth::user()->isClient())
        <div class="p-6">
            <div class="mx-12 my-4">
                <x-ui.dashboard-card
                    :route="route('allocation-calculator')"
                    :title="'Allocations Calculator'"
                    :linkTitle="'Go to calculator'"
                >
                    <x-slot name="icon">
                        <x-icons.calculator :class="'w-auto h-6 text-blue'"/>
                    </x-slot>{{__('Use the Allocations Calculator to determine how much you should put into each of your accounts based on revenue.')}}

                </x-ui.dashboard-card>
            </div>
        </div>

        <div class="p-6 border-t border-light_blue md:border-t-0 md:border-l">
            <div class="mx-12 my-4">
                <x-ui.dashboard-card
                    :route="route('businesses')"
                    :title="'Businesses'"
                    :linkTitle="'Go to businesses'"
                >
                    <x-slot name="icon">
                        <x-icons.case :class="'w-auto h-6 text-blue'"/>
                    </x-slot>
                    {{__('View all businesses you own, advise or are collaborating on. From here you can adjust accounts, rollout percentages and allocations.')}}
                </x-ui.dashboard-card>
            </div>
        </div>
    @endif

    @if(Auth::user()->isRegionalAdmin()
        || Auth::user()->isSuperAdmin()
        || Auth::user()->isAdvisor())
        <div class="p-6 border-t border-light_blue">
            <div class="mx-12 my-4">
                @php
                    if (Auth::user()->isRegionalAdmin()
                         && !Auth::user()->isSuperAdmin()
                         && !Auth::user()->isAdvisor()
                         && !Auth::user()->isClient()) {
                         $userCardTitle = 'Advisors' ;
                         $userCardLinkTitle =  'See advisors' ;
                    } else {
                         $userCardTitle = 'Users';
                         $userCardLinkTitle =  'See users';
                    }
                @endphp
                <x-ui.dashboard-card
                    :route="route('users')"
                    :title="$userCardTitle"
                    :linkTitle="$userCardLinkTitle"
                >
                    <x-slot name="icon">
                        <x-icons.users :class="'w-auto h-5 text-blue'"/>
                    </x-slot>
                    {{__('See an overview of all the users visible to you. Clients will only be able to see themselves.')}}
                </x-ui.dashboard-card>
            </div>
        </div>
    @endif

    <div class="p-6 border-t border-light_blue md:border-l
        @if(Auth::user()->isRegionalAdmin()
        || Auth::user()->isSuperAdmin()
        || Auth::user()->isAdvisor())
    @else
        md:border-t-0
        @endif">
        <div class="mx-12 my-4">

            <x-ui.dashboard-card
                :route="route('profile.show')"
                :title="'Profile Settings'"
                :linkTitle="'See profile'"
            >
                <x-slot name="icon">
                    <x-icons.lock :class="'w-auto h-6 text-blue'"/>
                </x-slot>
                {{__('Set up your profile, add a picture, log out of other sessions and set extra security via 2 factor authentication.')}}
            </x-ui.dashboard-card>

        </div>
    </div>
</div>
