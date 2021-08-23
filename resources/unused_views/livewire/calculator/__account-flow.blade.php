<td class="p-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
    <x-ui.input
        class="text-right"
        placeholder="0"
        :flow="$flow"
        :date="$date"
        wire:model.lazy="amount"
    />
</td>
