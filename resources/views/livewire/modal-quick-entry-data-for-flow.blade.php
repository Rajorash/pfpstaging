<div>
    <div class="text-bold">
        {{__('Quick Entry for Flow ":flow" (":account")', ['flow' => $accountFlow->label, 'account' => $bankAccount->name])}}
    </div>

    <livewire:quick-entry-data-for-flow :account-id="$accountId" :flow-id="$flowId" :modal-mode="true"/>
</div>
