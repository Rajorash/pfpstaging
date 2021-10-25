<div>
    <div class="text-bold">
        @if ($flowId)
            {{__('Update Flow ":flow" For ":account"', ['flow' => $flow->label, 'account' => $account->name])}}
        @else
            {{__('Create A New Flow For ":account"',[ 'account' => $account->name])}}
        @endif
    </div>

    <livewire:add-edit-flow :account-id="$accountId" :flow-id="$flowId" :modal-mode="true"/>

    {{-- <x-ui.button-normal wire:click="$emit('closeModal')" type="button" class="bg-gray-500">
        Close
    </x-ui.button-normal> --}}
</div>
