<?php

namespace App\Http\Livewire\Calculator;

use App\Models\Allocation;
use App\Models\BankAccount;
use Carbon\Carbon;
use Livewire\Component;

class AccountTransfer extends Component
{

    public $accountId;
    public BankAccount $account;
    public $amount;
    public $date;
    public $phase_id = 1;

    protected $listeners = ['updateAccountTransfer'];
    //
    public function mount($accountId, $date) {

        $this->accountId = $accountId;
        $this->account = BankAccount::find($accountId);
        $this->date = $date;
        if ($this->account->type == 'pretotal') {
            $this->amount = $this->account->getAllocationsTotalByDate($this->date);
        } else {
            ;
        }
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

    public function updateAccountTransfer(array $params)
    {
        if ($params['account_id'] == $this->accountId && Carbon::parse($params['date_str']) == $this->date) {
            $this->amount = $params['amount'];
            if ($this->account->flows->pluck('negative_flow', 'id')[$params['flow_id']]) {
                $this->amount *= -1;
            }

            return $this->render();
        }
    }

    /**
     * Functions carried out once $amount has finished updating
     *
     * @return void
     */
    public function updatedAmount() {
//        $this->store();
    }

}
