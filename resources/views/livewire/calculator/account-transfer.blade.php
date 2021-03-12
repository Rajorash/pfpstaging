<x-ui.input
    class="block text-right bg-blue-100"
    :account="$account"
    :date="$date"
    wire:model="amount"
    placeholder=0
    disabled="true"
    id="account_transfer_{{$account->id}}_{{substr($date,0,10)}}"
/>
