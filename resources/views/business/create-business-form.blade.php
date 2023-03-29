<div>
    @if(Auth::user()->isAdvisor())
        <x-ui.button-normal type="button"
                            wire:click="openBusinessForm"
                            wire:loading.attr="disabled">
            {{ __('Create Business') }}
        </x-ui.button-normal>

        <!-- Delete User Confirmation Modal -->
        <x-jet-dialog-modal wire:model="isOpen">
            <x-slot name="title">
                {{ __('Create Business') }}
            </x-slot>

            <x-slot name="content">
                <div class="text-left">
                    {{ __('To create a business you will need to expend a license. This license will be assigned against your total.') }}
                </div>

                @if ($failure)
                    <div class="mt-4 text-left text-red-700" x-data="{}">
                        {{$failureMessage}}
                    </div>
                @else
                    <div class="mt-4" x-data="{}"
                         x-on:creating-business.window="setTimeout(() => $refs.businessname.focus(), 250)">
                        <x-jet-input type="text" class="mt-1 block w-3/4"
                                     placeholder="{{ __('Business Name') }}"
                                     x-ref="businessname"
                                     wire:model.lazy="businessname"
                                     wire:keydown.enter="submitForm"
                        />
                        <x-jet-input-error for="businessname" class="mt-2 text-left"/>

                        <x-jet-input type="text" class="mt-1 block w-3/4"
                                     placeholder="{{ __('Owner Email') }}"
                                     x-ref="email"
                                     wire:model.lazy="email"
                                     wire:keydown.enter="submitForm"
                        />
                        <x-jet-input-error for="email" class="mt-2 text-left"/>

                        <div class="flex w-3/4">
                            <div class="w-1/2">
                                <x-jet-input type="date" class="mt-1 block w-full"
                                             x-ref="phaseStart"
                                             min="{{$phaseStartMin}}"
                                             wire:model.lazy="phaseStart"
                                />
                            </div>
                            <div class="w-1/2 pt-3 text-left pl-2">{{__('Date of start a phase')}}</div>
                        </div>
                        <x-jet-input-error for="phaseStart" class="mt-2 text-left"/>
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-ui.button-secondary
                    class="ml-2   bg-blue-800 text-white "
                    wire:click="$toggle('isOpen')"
                    wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-ui.button-secondary>

                @if (!$failure)
                    <x-ui.button-primary
                        class="ml-2 bg-blue-800 text-white "
                        wire:click="submitForm"
                        wire:loading.attr="disabled">
                        {{ __('Create Business') }}
                    </x-ui.button-primary>
                @endif
            </x-slot>
        </x-jet-dialog-modal>
    @endif
</div>
