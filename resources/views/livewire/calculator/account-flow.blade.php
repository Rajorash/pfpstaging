@php
    $total_cell_key = 'account_total_'.$account_id.'_'.substr($date,0,10);
    $transfer_cell_key = 'account_transfer_'.$account_id.'_'.substr($date,0,10);
    $value_cell_key = 'account_value_'.$account_id.'_';//.substr($date,0,10);
    $cnt = 0;
    $onChange = '';
    if ($flow->account->type == 'pretotal') {
        $onChange .= 'component_t = document.getElementById(\''.$transfer_cell_key.'\');
            lw_t_component = window.livewire.find(component_t.getAttribute(\'wire:id\'));
            setTimeout(function(){lw_t_component.call(\'updateAccountTransfer\');}, 1100);
            ';
    }
    if ($flow->account->type != 'revenue') {
        foreach ($datesRange as $a_date) {
            $onChange .= 'component'.++$cnt.' = document.getElementById(\''.$value_cell_key.$a_date.'\');
            lw_'.$cnt.'_component = window.livewire.find(component'.$cnt.'.getAttribute(\'wire:id\'));
            setTimeout(function(){lw_'.$cnt.'_component.call(\'updateAccountValue\');}, '.(1000+(100*$cnt)).');
            ';
        }
    }
@endphp
<td class="p-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
    <x-ui.input
        class="text-right"
        placeholder="0"
        :flow="$flow"
        :date="$date"
        wire:model.lazy="amount"
        onchange="
            component = document.getElementById('{{$total_cell_key}}');
            lw_component = window.livewire.find(component.getAttribute('wire:id'));
            setTimeout(function(){lw_component.call('updateAccountTotal');}, 1000);

            {{$onChange}}
        " />
</td>
