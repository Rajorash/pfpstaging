<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Carbon\Carbon;
use Livewire\Component;

class AccountValue extends Component
{

    public $accountId;
    public BankAccount $account;
    public $allocation;
    public $amount;
    public $date;
    public $phase_id = 1;

    protected $listeners = ['updateAccountValue'];

    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->allocation = self::getAllocation($date);
        $this->phase_id = $this->account->business->getPhaseIdByDate($date);
        $this->amount = $this->account->getAllocationsTotalByDate($this->date, $this->phase_id) +
            ($this->allocation
            ? number_format($this->allocation->amount, 0, '.', '')
            : 0);
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

    public function updateAccountValue()
    {
        $currentAmount = $this->account->getAllocationsTotalByDate($this->date, $this->phase_id);

        $previousDate = clone $this->date;
        $previousAllocation = self::getAllocation($previousDate->subDays(1));
        if($previousAllocation) {
            if ($this->amount != ($currentAmount + $previousAllocation->amount)) {
                $this->amount = $previousAllocation->amount + $currentAmount;
                $this->store();
                return $this->render();
            }
        } else if ($this->amount != $currentAmount) {
            $this->amount = $currentAmount;
            $this->store();
            return $this->render();
        }
    }

    /**
     * Functions carried out once $amount has finished updating
     *
     * @return void
     */
    public function updatedAmount() {
        $s =1;
//        $this->store();
//        $this->emit('updateAccountTotal');
    }

}
