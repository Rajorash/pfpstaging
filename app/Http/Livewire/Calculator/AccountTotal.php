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

    protected $listeners = ['updateAccountTotal'];

    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        $this->amount = $this->account->getAllocationsTotalByDate($this->date);
    }

    /**
     * Update the total if a flow value has changed for the given date
     *
     * @param  array  $params
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function updateRevenueAccountTotal(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $newAmount = $this->account->getAllocationsTotalByDate($this->date);
            if ($this->amount != $newAmount) {
                $this->amount = $newAmount;
                $this->store();
                return $this->render();
            }
        }
    }

    public function updatePretotalAccountTotal(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $this->amount = $this->account->getAllocationsTotalByDate($this->date);

            return $this->render();
        }
    }

    public function updatePrerealAccountTotal(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $this->amount = $this->account->getAllocationsTotalByDate($this->date);

            return $this->render();
        }
    }

    public function updatePostrealAccountTotal(array $params)
    {
        $this->updatePrerealAccountTotal($params);
    }

    public function updateSalestaxAccountTotal(array $params)
    {
        $this->updatePrerealAccountTotal($params);
    }

    public function updateAccountTotal()
    {
        $this->amount = $this->account->getAllocationsTotalByDate($this->date);

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
