<?php

namespace App\Http\Livewire;

use App\Http\Controllers\RecurringTransactionsController;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use Carbon\Carbon;
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

    public function deleteAccount()
    {
        $account = BankAccount::find($this->confirmingId);

        if ($account) {

            $account->delete();

            $this->confirmingId = null;

            $this->mount();
            $this->render();
        }
    }

    public function confirmDeleteAccount($accountId)
    {
        $this->confirmingId = $accountId;
    }

    public function closeModal()
    {
        $this->confirmingId = null;
        $refresh;
    }

    public function render()
    {
        return view('accounts.business-account-show');
    }
}
