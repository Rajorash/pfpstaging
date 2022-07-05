<div>
    <div class="pb-6 text-2xl text-black text-bold text-center text-red-700">
        @if ($flowMessage)
            {{ $flowMessage }}
        @else
            {{__('Error, Flow not found')}}
        @endif
    </div>

    <div class="table w-full mt-4">
        <div class="table-row">
            <div class="table-cell w-1/2 pb-4 text-left">
                <x-ui.button-secondary class="mr-4 uppercase uncheckbox" wire:click="$emit('closeModal')" type="button">
                    {{__('Cancel')}}
                </x-ui.button-secondary>
            </div>
            <div class="table-cell w-1/2 pb-4 text-right">
                <x-ui.button-danger wire:loading.attr="disabled" wire:click="$emit('confirmDelete')" class="uppercase checkbox"
                                    type="button">
                    {{__('Yes, delete the Business')}}
                </x-ui.button-danger>
            </div>
        </div>
    </div>
</div>
