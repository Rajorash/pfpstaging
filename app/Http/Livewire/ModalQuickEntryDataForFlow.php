<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use LivewireUI\Modal\ModalComponent;

class ModalQuickEntryDataForFlow extends ModalComponent
{

    public int $accountId = 0;
    public int $flowId = 0;
    public BankAccount $bankAccount;
    public AccountFlow $accountFlow;

    public function mount($accountId, $flowId = 0)
    {
        $this->accountId = $accountId;
        $this->flowId = $flowId;

        $this->bankAccount = BankAccount::findOrFail($this->accountId);
        $this->accountFlow = AccountFlow::findOrFail($this->flowId);
    }

    public function render()
    {
        return view('livewire.modal-quick-entry-data-for-flow');
    }
}
