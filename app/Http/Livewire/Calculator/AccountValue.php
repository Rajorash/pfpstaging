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
        $this->allocation = self::getAllocation($accountId, $date);
        $this->amount = $this->allocation
            ? number_format($this->allocation->amount, 0, '.', '')
            : 0;

    }

    private function getAllocation($accountId, $date) {
        $allocation = Allocation::where([
            ['allocatable_type', '=', 'App\Models\BankAccount'],
            ['allocatable_id', '=', $accountId],
            ['allocation_date', '=', $date]
        ])->first();

        return $allocation ?? null;
    }

    public function render()
    {
        return view('livewire.calculator.account-value');
    }

    public function store() {
        $this->validate([
            'amount' => 'numeric|nullable'
        ]);

        $data = array(
            'amount' => $this->amount
        );

        $this->account->allocate([
            'amount' => $this->amount,
            'phase_id' => $this->phase_id,
            'allocation_date' => $this->date
        ]);
        // // if a valid amount is entered, store it in the database
        // $allocation = Allocation::updateOrCreate([
        //     'allocatable_id' => $this->flowId,
        //     'allocatable_type' => 'App\Models\AccountFlow',
        //     'allocation_date' => $this->date,
        //     'phase_id' => $this->phase_id,
        // ],$data);
    }

    public function updatedAmount() {
        $this->store();
    }

}
