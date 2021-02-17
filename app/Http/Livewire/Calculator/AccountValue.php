<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Livewire\Component;

class AccountValue extends Component
{

    public $accountId;
    public BankAccount $account;
    public $allocation;
    public $amount;
    public $date;
    public $phase_id = 1;

    //
    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->allocation = self::getAllocation($date);
        $this->amount = $this->allocation
            ? number_format($this->allocation->amount, 0, '.', '')
            : 0;

    }

    /**
     * get the allocation for the account
     *
     * @param [type] $accountId
     * @param [type] $date
     * @return Allocation|null
     */
    private function getAllocation($date) {

        $allocation = $this->account->getAllocationByDate($date);

        return $allocation ?? null;
    }

    /**
     * The livewire component render method
     *
     * @return void
     */
    public function render()
    {
        return view('livewire.calculator.account-value');
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

    /**
     * Functions carried out once $amount has finished updating
     *
     * @return void
     */
    public function updatedAmount() {
        $this->store();
        $this->emit('updateAccountTotal');
    }

}
