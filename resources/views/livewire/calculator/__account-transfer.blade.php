<x-ui.input
    class="block text-right bg-blue"
    :account="$account"
    :date="$date"
    wire:model="amount"
    placeholder=0
    disabled="true"
    :uid="$uid"
/>
