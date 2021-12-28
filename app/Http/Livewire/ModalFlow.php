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
    public string $routeName = '';
    public BankAccount $account;
    public AccountFlow $flow;

    protected $listeners = [
        'reloadRevenueTable' => 'reloadRevenueTable',
    ];

    /**
     * @param $accountId
     * @param  int  $flowId
     * @param  false  $defaultNegative
     * @param  string  $routeName
     */
    public function mount($accountId, int $flowId = 0, bool $defaultNegative = false, string $routeName = '')
    {
        $this->accountId = $accountId;
        $this->flowId = $flowId;
        $this->defaultNegative = $defaultNegative;
        $this->routeName = $routeName;

        $this->account = BankAccount::findOrFail($this->accountId);

        if ($this->flowId) {
            $this->flow = AccountFlow::findOrFail($this->flowId);
        }
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
        return view('livewire.modal-flow');
    }
}
