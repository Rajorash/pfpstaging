<x-ui.input
    class="block text-right account_value_{{substr($date,0,10)}}"
    :account="$account"
    :date="$date"
    wire:model="amount"
    placeholder=0
    id="account_value_{{$account->id}}_{{substr($date,0,10)}}"
/>
