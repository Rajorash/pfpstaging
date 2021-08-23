<div class="livewire-wrapper">

    @if(Auth::user()->isAdvisor())
        <form wire:submit.prevent="store">
            <div class="table w-full mt-10">
                <div class="table-row @if ($business->owner_id) hidden @endif">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Client (owner)') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <select name="" id="" wire:model="userId"
                                class="w-full form-input border-light_blue
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                    rounded-md shadow-sm"
                                @if ($business->owner_id)
                                disabled
                            @endif

                        >
                            <option value="0">:: Invite by email</option>
                            @foreach ($advisorsClients as $client)
                                <option value="{{$client->id}}">{{$client->name}} ({{$client->email}})
                                </option>
                            @endforeach
                        </select>

                        <x-jet-input-error for="userId" class="text-left mt-2"/>

                    </div>
                </div>

                @if ($business->owner_id)
                    <div class="table-row">
                        <div class="table-cell w-1/4 pb-4 text-left">
                            {{ __('Client (owner)') }}
                        </div>
                        <div class="table-cell w-3/4 pb-4 text-left">
                            {{$business->owner->name}}
                        </div>
                    </div>
                @endif

                <div class="table-row
                @if($userId) hidden @endif
                @if ($business->owner_id) hidden @endif
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

                <div class="table-row">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Available licenses:') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        {{$availableLicenses}}

                        @if(!$allowToSetLicense)
                            <p class="text-red-700">
                                You must revoke some licenses from another business before set License here
                            </p>
                        @endif
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
                        @if ($this->business->license)
                            {{ __('Active License') }}
                        @else
                            {{ __('Create License') }}
                        @endif
                    </div>
                    <div class="table-cell w-3/4 pb-4 text-left">
                        <input type="checkbox"
                               wire:model="activeLicense"
                               @if(!$allowToSetLicense) disabled @endif
                               class="disabled:opacity-50"
                               id="activeLicense"/>
                    </div>
                </div>

                <div class="table-row @if(!$activeLicense) hidden @endif">
                    <div class="table-cell w-1/4 pb-4 text-left">
                        {{ __('Expiration date') }}
                    </div>
                    <div class="table-cell w-3/4 pb-4">
                        <x-jet-input
                            id="expired"
                            class="w-full"
                            type="datetime-local"
                            name="expired"
                            value="{{$expired}}"
                            wire:model.lazy="expired"
                        />
                        <x-jet-input-error for="expired" class="mt-2 text-left"/>
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
                        <x-ui.button-normal class="uppercase" type="button" wire:loading.attr="disabled">
                            Update Licenses
                        </x-ui.button-normal>
                    </div>
                </div>
            </div>
        </form>
    @else
        Access denied
    @endif
</div>
