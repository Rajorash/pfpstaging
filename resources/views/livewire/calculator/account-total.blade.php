<x-ui.input
    class="block text-right bg-yellow-100"
    :account="$account"
    :date="$date"
    wire:model="amount"
    placeholder=0
    disabled="true"
    id="account_total_{{$account->id}}_{{substr($date,0,10)}}"
/>
