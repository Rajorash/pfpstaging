<div>
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

            <div class="mt-4" x-data="{}" {{-- x-on:confirming-delete-user.window="setTimeout(() => $refs.businessname.focus(), 250)" --}}>
                <x-jet-input type="text" class="mt-1 block w-3/4"
                            placeholder="{{ __('Business Name') }}"
                            x-ref="businessname"
                            wire:model.defer="businessname"
                            {{-- wire:keydown.enter="deleteUser"  --}}
                            />

                <x-jet-input-error for="email" class="mt-2" />
                <x-jet-input type="text" class="mt-1 block w-3/4"
                            placeholder="{{ __('Owner Email') }}"
                            x-ref="email"
                            wire:model.defer="email"
                            {{-- wire:keydown.enter="deleteUser"  --}}
                            />

                <x-jet-input-error for="email" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('isOpen')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-ui.button-normal type="button"
            class="ml-2"
            wire:click="createBusiness" wire:loading.attr="disabled">
                {{ __('Create Business') }}
            </x-ui.button-normal>
        </x-slot>
    </x-jet-dialog-modal>

</div>
