<?php

namespace App\Http\Livewire;

use App\Http\Controllers\RecurringTransactionsController;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Livewire\Component;

class BusinessAccountShow extends Component
{

    private $deletableTypes = ['account', 'flow'];

    public $business;
    public $accounts;
    public $itemToDelete;
    public $confirmationTitle;
    public $confirmationMessage;

    public function mount()
    {
        $this->business->refresh();
        $this->accounts = $this->business->accounts;
    }

    public function deleteItem()
    {
        if($this->itemToDelete['type'] == 'account') {
            $itemToDelete = BankAccount::find($this->itemToDelete['id']);
            $this->confirmationMessage = 'Are you certain you wish to delete this account, all flows and data will also be removed.';
        }

        if($this->itemToDelete['type'] == 'flow') {
            $itemToDelete = AccountFlow::find($this->itemToDelete['id']);
            $this->confirmationMessage = 'Are you certain you wish to delete this flow, all data will also be removed.';
        }

        if ($itemToDelete) {
            $this->confirmationTitle = 'Confirm '.ucfirst($itemToDelete['type']).' Deletion?';

            $itemToDelete->delete();

            $this->itemToDelete = null;

            $this->mount();
            $this->render();
        }
    }

    public function confirmDeleteItem($type, $id)
    {
        if (in_array($type, $this->deletableTypes)) {
            $this->itemToDelete = ['type' => $type, 'id' => $id];

            $this->confirmationTitle = 'Confirm '.ucfirst($type).' Deletion?';

            if($type == 'account') {
                $this->confirmationMessage = 'Are you certain you wish to delete this account, all flows and data will also be removed.';
            }

            if($type == 'flow') {
                $this->confirmationMessage = 'Are you certain you wish to delete this flow, all data will also be removed.';
            }

        }
    }

    public function closeModal()
    {
        $this->reset(['itemToDelete', 'confirmationTitle', 'confirmationMessage']);
        $refresh;
    }

    public function render()
    {
        return view('accounts.business-account-show');
    }
}
