<div class="p-6 sm:px-20 bg-white border-b border-light_blue">
    <div class="mt-8  text-blue">
        <x-icons.two-users :class="'w-8 h-auto'"/>
    </div>

    <div class="mt-4 text-2xl mt-2 text-blue">
        Welcome to the PF Prophet!
    </div>

    <div class="mt-2">
        This is a predictive financial tool to help you proactively plan your businesses financial needs.
    </div>
</div>

<div class="bg-dashboard grid grid-cols-1 md:grid-cols-2">
    <div class="p-6">
        <div class="mx-12">
            <div class="mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     class="w-auto h-6 text-blue">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>

            <div class="text-lg text-dark_gray2 leading-7">
                <a href="{{ route('allocation-calculator') }}">Allocations
                    Calculator</a>
            </div>

            <div class="mt-2 text-sm">
                Use the Allocations Calculator to determine how much you should put into each of your accounts based on
                revenue.
            </div>

            <a href="{{ route('allocation-calculator') }}">
                <div class="mt-3 flex items-center text-sm font-normal text-blue">
                    <div>Go to calculator</div>

                    <div class="ml-2 text-blue">
                        <x-icons.arrow-right/>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="p-6 border-t border-light_blue md:border-t-0 md:border-l">
        <div class="mx-12">

            <div class="mb-2">
                <x-icons.case :class="'w-auto h-6 text-blue'"/>
            </div>

            <div class="text-lg text-dark_gray2 leading-7">
                <a href="{{ route('businesses') }}">Businesses</a>
            </div>

            <div class="mt-2 text-sm">
                View all businesses you own, advise or are collaborating on. From here you can adjust accounts, rollout
                percentages and allocations.
            </div>

            <a href="{{ route('businesses') }}">
                <div class="mt-3 flex items-center text-sm font-normal text-blue">
                    <div>Go to businesses</div>

                    <div class="ml-2 text-blue">
                        <x-icons.arrow-right/>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="p-6 border-t border-light_blue">
        <div class="mx-12">
            <div class="mb-2">
                <x-icons.users :class="'w-auto h-5 text-blue'"/>
            </div>

            <div class="text-lg text-dark_gray2 leading-7">
                <a href="{{ route('users') }}">Users</a>
            </div>

            <div class="mt-2 text-sm">
                See an overview of all the users visible to you. Clients will only be able to see themselves.
            </div>

            <a href="{{ route('users') }}">
                <div class="mt-3 flex items-center text-sm font-normal text-blue">
                    <div>See users</div>

                    <div class="ml-2 text-blue">
                        <x-icons.arrow-right/>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="p-6 border-t border-light_blue md:border-l">

        <div class="mx-12">
            <div class="mb-2">
                <x-icons.lock :class="'w-auto h-6 text-blue'"/>
            </div>

            <div class="text-lg text-dark_gray2 leading-7">
                <a href="{{ route('profile.show') }}">Profile Settings</a>
            </div>

            <div class="mt-2 text-sm">Set up your profile, add a picture, log out of other sessions and set extra
                security via 2 factor authentication.
            </div>

            <a href="{{ route('profile.show') }}">
                <div class="mt-3 flex items-center text-sm font-normal text-blue">
                    <div>See profile</div>

                    <div class="ml-2 text-blue">
                        <x-icons.arrow-right/>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
