<div>
    <form wire:submit.prevent="store">
        <div class="table w-full mt-10">

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Label') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="label" class="w-full" type="text" name="label"
                                 wire:model.defer="label" wire:loading.attr="disabled"
                                 required autofocus/>
                    <x-jet-input-error for="label" class="mt-2"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Certainly') }}
                </div>
                <div class="table-cell w-3/4 pb-4">
                    <x-jet-input id="certainly" class="w-full" type="number"
                                 min="5" max="100" step="5"
                                 name="certainly"
                                 wire:model.defer="certainly"
                                 wire:loading.attr="disabled"
                                 required/>
                    <x-jet-input-error for="label" class="mt-2"/>
                </div>
            </div>

            <div class="table-row">
                <div class="table-cell w-1/4 pb-4 text-left">
                    {{ __('Flow Type') }}
                </div>
                <div class="table-cell w-3/4 pb-4 text-left">
                    <input class="form-radio" type="radio" name="negative_flow" id="flow-in"
                           autocomplete="off"
                           wire:model.defer="negative_flow"
                           wire:loading.attr="disabled"
                           value="0"/>
                    <label for="flow-in">{{__('Positive')}}</label>
                    <input class="form-radio ml-4" type="radio" name="negative_flow" id="flow-out"
                           autocomplete="off"
                           wire:model.defer="negative_flow"
                           wire:loading.attr="disabled"
                           value="1"/>
                    <x-jet-input-error for="negative_flow" class="mt-2"/>
                    <label for="flow-out">{{__('Negative')}}</label>
                </div>
            </div>

        </div>

        <div class="table w-full mt-4">
            <div class="table-row">
                <div class="table-cell w-full pb-4 text-right">
                    <x-ui.button-normal wire:loading.attr="disabled" class="uppercase" type="button">
                        @if($flowId)
                            {{__('Update Flow')}}
                        @else
                            {{__('Create Flow')}}
                        @endif
                    </x-ui.button-normal>
                </div>
            </div>
        </div>
    </form>
</div>
