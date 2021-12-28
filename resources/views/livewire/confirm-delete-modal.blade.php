<div>
    <div class="pb-6 text-2xl text-black text-bold text-center text-red-700">
        @if ($flowId)
            {{__('Are you really would like to delete a Flow ":flow"', ['flow' => $flow->label])}}
        @else
            {{__('Error, Flow not found')}}
        @endif
    </div>

    <div class="table w-full mt-4">
        <div class="table-row">
            <div class="table-cell w-1/2 pb-4 text-left">
                <x-ui.button-secondary class="mr-4 uppercase" wire:click="$emit('closeModal')" type="button">
                    {{__('Cancel')}}
                </x-ui.button-secondary>
            </div>
            <div class="table-cell w-1/2 pb-4 text-right">
                <x-ui.button-danger wire:loading.attr="disabled" wire:click="$emit('confirmDelete')" class="uppercase"
                                    type="button">
                    {{__('Yes, delete the Flow')}}
                </x-ui.button-danger>
            </div>
        </div>
    </div>
</div>
