<div class="livewire-wrapper">
    <form wire:submit.prevent="save">
        <div class="table w-full mt-10">

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Name') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="name" class=" w-full" type="text" name="name"
                    required autofocus wire:model="user.name"/>
                    <x-jet-input-error for="user.name" class="mt-2 text-left text-left"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('E-Mail Address') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="email" class=" w-full" type="email" name="email"
                    required wire:model="user.email"/>
                    <x-jet-input-error for="user.email" class="mt-2 text-left"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Title') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="title" class=" w-full" type="text" name="title"
                    autofocus wire:model="user.title"/>
                    <x-jet-input-error for="user.title" class="mt-2 text-left"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Responsibility') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="responsibility" class=" w-full" type="text" name="responsibility"
                    autofocus wire:model="user.responsibility"/>
                    <x-jet-input-error for="user.responsibility" class="mt-2 text-left"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Timezone:') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <select name="timezone" id="timezone" wire:model="user.timezone"
                    class="w-full form-input border-light_blue
                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    rounded-md shadow-sm">
                    <option>Select your timezone</option>
                    @foreach (timezone_identifiers_list(64) as $timezone)
                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="user.timezone" class="mt-2 text-left"/>
            </div>
        </div>
    </div>

    <div class="table w-full mt-4">
        <div class="table-row">
            <div class="table-cell w-full pb-4 text-right">
                <x-ui.button-normal class="uppercase" type="button" wire:loading.attr="disabled">
                    Save User
                </x-ui.button-normal>
            </div>
        </div>
    </div>
</form>
</div>

