<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use LivewireUI\Modal\ModalComponent;

class ConfirmDeleteModal extends ModalComponent
{
    public int $flowId;
    public int $accountId;
    public string $routeName;
    public AccountFlow $flow;
    public BankAccount $account;

    protected $listeners = [
        'confirmDelete' => 'confirmDelete'
    ];

    /**
     * @param  int  $flowId
     * @param  int  $accountId
     */
    public function mount(int $flowId, int $accountId, string $routeName)
    {
        $this->flowId = $flowId;
        $this->accountId = $accountId;
        $this->routeName = $routeName;
        $this->flow = AccountFlow::findOrFail($this->flowId);
        $this->account = BankAccount::findOrFail($this->accountId);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Redirector
     */
    public function confirmDelete()
    {
        if (!$this->flow) {
            $this->flow = AccountFlow::findOrFail($this->flowId);
        }

        $this->flow->delete();

        $this->forceClose()->closeModal();

        return redirect()->route($this->routeName, ['business' => $this->account->business_id]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.confirm-delete-modal');
    }
}
