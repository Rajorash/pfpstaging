@php
    $cell_key = 'account_total_'.$flow->account_id.'_'.substr($date,0,10);
@endphp
<td class="p-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
    <x-ui.input
        class="text-right"
        placeholder="0"
        :flow="$flow"
        :date="$date"
        wire:model.lazy="amount"
        onchange="
            let component = document.getElementById({{$cell_key}});
            let lw_component = window.livewire.find(component.getAttribute('wire:id'));
            lw_component.call('updatePrerealAccountTotal');
        " />
</td>
