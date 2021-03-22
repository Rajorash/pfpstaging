<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Livewire\Component;
use Carbon\Carbon;

class AccountTotal extends Component
{
    public $accountId;
    public BankAccount $account;
    public $allocation;
    public $amount;
    public $date;
    public $phase_id = 1;
    public $uid;

    public function mount($accountId, $date)
    {
        $this->uid = 'account_total_'.$accountId.'_'.substr($date,0,10);
        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->phase_id = $this->account->business->getPhaseIdByDate($date);
        $this->amount = $this->account->getAllocationsTotalByDate($this->date, $this->phase_id);
    }

    protected function getListeners()
    {
        return ['updateAccountTotal:'.$this->uid => 'updateAccountTotal'];
    }

    /**
     * Update the total if a flow value has changed for the given date
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function updateAccountTotal($params)
    {
        $this->amount = $this->account->getAllocationsTotalByDate($this->date, $this->phase_id);
        $this->store();

        $this->emit('updateAccountTransfer:account_transfer_'.$params['salestax_id'].'_'.substr($this->date,0,10));
        $this->emit('updateAccountTransfer:account_transfer_'.$params['pretotal_id'].'_'.substr($this->date,0,10));

        return $this->render();
    }

    /**
     * Validate and store the Allocation
     *
     * @return void
     */
    public function store() {
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

    public function render()
    {
        return view('livewire.calculator.account-total');
    }

}
