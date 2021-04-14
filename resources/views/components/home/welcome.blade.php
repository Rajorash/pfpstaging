<div class="p-6 sm:px-20 bg-white border-b border-light_blue">
    <div class="mt-8 text-blue">
        <x-icons.two-users :class="'w-8 h-auto'"/>
    </div>

    <div class="mt-4 text-2xl mt-2 text-blue">
        Welcome to the PF Prophet!
    </div>

    <div class="mt-2 mb-2">
        This is a predictive financial tool to help you proactively plan your businesses financial needs.
    </div>
</div>

<div class="bg-dashboard grid grid-cols-1 md:grid-cols-2">
    <div class="p-6">
        <div class="mx-12 my-4">
            <x-ui.dashboard-card
                :route="route('allocation-calculator')"
                :title="'Allocations Calculator'"
                :linkTitle="'Go to calculator'"
            >
                <x-slot name="icon">
                    <x-icons.calculator :class="'w-auto h-6 text-blue'"/>
                </x-slot>
                Use the Allocations Calculator to determine how much you should put into each of your accounts based on
                revenue.
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
                View all businesses you own, advise or are collaborating on. From here you can adjust accounts, rollout
                percentages and allocations.
            </x-ui.dashboard-card>
        </div>
    </div>

    <div class="p-6 border-t border-light_blue">
        <div class="mx-12 my-4">
            <x-ui.dashboard-card
                :route="route('users')"
                :title="'Users'"
                :linkTitle="'See users'"
            >
                <x-slot name="icon">
                    <x-icons.users :class="'w-auto h-5 text-blue'"/>
                </x-slot>
                See an overview of all the users visible to you. Clients will only be able to see themselves.
            </x-ui.dashboard-card>
        </div>
    </div>

    <div class="p-6 border-t border-light_blue md:border-l">
        <div class="mx-12 my-4">

            <x-ui.dashboard-card
                :route="route('profile.show')"
                :title="'Profile Settings'"
                :linkTitle="'See profile'"
            >
                <x-slot name="icon">
                    <x-icons.lock :class="'w-auto h-6 text-blue'"/>
                </x-slot>
                Set up your profile, add a picture, log out of other sessions and set extra
                security via 2 factor authentication.
            </x-ui.dashboard-card>

        </div>
    </div>
</div>
