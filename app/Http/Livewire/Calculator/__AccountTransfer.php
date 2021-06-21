<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class __AccountTransfer extends Component
{
    use GettersTrait;

    public $accountId;
    public BankAccount $account;
    public $amount;
    public $date;
    public $phase_id = 1;
    public $uid;

    public function mount($accountId, $date)
    {
        $this->uid = 'account_transfer_'.$accountId.'_'.substr($date, 0, 10);
        $this->accountId = $accountId;
//        $this->account = BankAccount::find($accountId);
        $this->account = $this->getBackAccount($accountId);
        $this->date = $date;
        $this->phase_id = $this->account->business->getPhaseIdByDate($date);
        $this->amount = $this->account->getTransferAmount($this->date, $this->phase_id);
    }

    protected function getListeners()
    {
        return ['updateAccountTransfer:'.$this->uid => 'updateAccountTransfer'];
    }

    /**
     * The livewire component render method
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.calculator.account-transfer');
    }

    /**
     * Validate and store the Allocation
     *
     * @return void
     */
    public function store()
    {
        $this->validate([
            'amount' => 'numeric|nullable'
        ]);

        $data = array(
            'amount' => $this->amount
        );

        $values = [
            $this->amount,
            $this->date,
            $this->phase_id
        ];

        $this->account->allocate(...$values);
    }

    public function updateAccountTransfer()
    {
        $this->amount = $this->account->getTransferAmount($this->date, $this->phase_id);

        return $this->render();
    }

    /**
     * Functions carried out once $amount has finished updating
     *
     * @return void
     */
    public function updatedAmount()
    {
        $this->store();
    }

}
