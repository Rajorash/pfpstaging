<x-ui.input
    class="block text-right bg-blue-100"
    :account="$account"
    :date="$date"
    wire:model="amount"
    placeholder=0
    disabled="true"
    :uid="$uid"
/>
