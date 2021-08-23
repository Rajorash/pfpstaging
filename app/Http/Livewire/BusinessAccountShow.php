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

//        //TODO: remove, its only for debug
//        $recurringAll = [];
//        foreach ($this->accounts as $account) {
//            foreach ($account->flows as $flow) {
//                if ($flow->recurringTransactions->count()) {
//                    $RecurringTransactionsController = new RecurringTransactionsController();
//                    $recurringAll[$flow->id] = $RecurringTransactionsController
//                        ->getAllFlowsForecasts($flow->recurringTransactions, '2021-09-01', '2022-03-01');
//                }
//            }
//        }
//        dd($recurringAll);
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
