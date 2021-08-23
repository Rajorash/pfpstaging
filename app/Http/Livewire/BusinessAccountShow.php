<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use Livewire\Component;

class BusinessAccountShow extends Component
{

    public $business;
    public $accounts;
    public $confirmingId;

    public function mount()
    {
        $this->business->refresh();
        $this->accounts = $this->business->accounts->load('flows');
    }

    public function deleteAccount($accountId)
    {
        $account = BankAccount::find($accountId);

        if ($account) {
            $account->delete();
            $this->mount();
            $this->render();
        }
    }

    public function confirmDeleteAccount($accountId)
    {
        $this->confirmingId = $accountId;
    }

    public function render()
    {
        return view('accounts.business-account-show');
    }
}
