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

    protected $listeners = ['reloadRevenueTable' => 'reloadRevenueTable'];

    /**
     * @param  int  $accountId
     * @param  int  $flowId
     */
    public function mount(int $accountId, int $flowId = 0)
    {
        $this->accountId = $accountId;
        $this->flowId = $flowId;

        $this->bankAccount = BankAccount::findOrFail($this->accountId);
        $this->accountFlow = AccountFlow::findOrFail($this->flowId);
    }

    public function reloadRevenueTable()
    {
        $this->closeModal();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.modal-quick-entry-data-for-flow');
    }
}
