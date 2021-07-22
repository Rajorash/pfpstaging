<div class="livewire-wrapper">

    @if(Auth::user()->isAdvisor())
        <form wire:submit.prevent="store">


            <div class="table w-full mt-10">

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Business name') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="businessName"
                            class="w-full"
                            type="text"
                            name="businessName"
                            wire:model.lazy="businessName"
                        />
                        <x-jet-input-error for="businessName" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Current Client (owner)') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        {{$business->owner->name}}
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('New Client (owner)') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <select name="" id="" wire:model="userId"
                                class="w-full form-input border-light_blue
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                    rounded-md shadow-sm"
                            {{--                                @if ($business->owner_id)--}}
                            {{--                                disabled--}}
                            {{--                            @endif--}}

                        >
                            <option value="0">:: Invite by email or leave as is</option>
                            @foreach ($advisorsClients as $client)
                                <option value="{{$client->id}}">{{$client->name}} ({{$client->email}})
                                </option>
                            @endforeach
                        </select>

                        <x-jet-input-error for="userId" class="text-left mt-2"/>

                    </div>
                </div>

                <div class="table-row
                @if($userId) hidden @endif
                    ">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Type Email of Existing Client') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="email"
                            class="w-full"
                            type="email"
                            name="email"
                            wire:model.lazy="email"
                        />
                        <x-jet-input-error for="email" class="mt-2 text-left"/>
                    </div>
                </div>

                @if($this->business->license && $this->business->license->account_number)
                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('License number') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4 text-left">
                            {{$this->business->license->account_number}}
                        </div>
                    </div>
                @endif

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{__('License status')}}

                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        @if ($this->business->license)
                            {{ __('License is Active') }}
                        @else
                            {{ __('License is Inactive') }}
                        @endif
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Invite an Advisor to collaborate (email)') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="email_collaborate"
                            class="w-full"
                            type="email"
                            name="email_collaborate"
                            wire:model.lazy="email_collaborate"
                        />
                        <x-jet-input-error for="email_collaborate" class="mt-2 text-left"/>
                    </div>
                </div>

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Delete business') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        <input type="checkbox"
                               wire:model="iWouldLikeToDelete"
                               id="iWouldLikeToDelete"/>
                        <label for="iWouldLikeToDelete"
                               class="pl-2">{{__('Mark this, if you would like to delete current Business')}}</div>
                </div>
            </div>


            @if ($failure)
                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left"></div>
                    <div class="table-cell w-3/4 pb-4 text-red-700 text-left">
                        {{$failureMessage}}
                    </div>
                </div>
    @endif

</div>

<div class="table w-full mt-4">
    <div class="table-row">
        <div class="table-cell w-full pb-4 text-right">
            @if($iWouldLikeToDelete)
                <x-ui.button-normal class="uppercase" type="button" wire:loading.attr="disabled">
                    Delete Business
                </x-ui.button-normal>
            @else
                <x-ui.button-normal class="uppercase" type="button" wire:loading.attr="disabled">
                    Update Business
                </x-ui.button-normal>
            @endif
        </div>
    </div>
</div>
</form>
@else
    Access denied
    @endif
    </div>
