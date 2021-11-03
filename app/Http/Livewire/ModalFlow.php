<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use LivewireUI\Modal\ModalComponent;

class ModalFlow extends ModalComponent
{

    public int $accountId = 0;
    public int $flowId = 0;
    public bool $defaultNegative = false;
    public BankAccount $account;
    public AccountFlow $flow;

    protected $listeners = ['reloadRevenueTable' => 'reloadRevenueTable'];

    public function mount($accountId, $flowId = 0, $defaultNegative = false)
    {
        $this->accountId = $accountId;
        $this->flowId = $flowId;
        $this->defaultNegative = $defaultNegative;

        $this->account = BankAccount::findOrFail($this->accountId);

        if ($this->flowId) {
            $this->flow = AccountFlow::findOrFail($this->flowId);
        }
    }

    public function reloadRevenueTable() {
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modal-flow');
    }
}
