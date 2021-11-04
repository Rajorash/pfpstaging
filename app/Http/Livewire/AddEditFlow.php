<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use Livewire\Component;

class AddEditFlow extends Component
{
    public int $flowId = 0;
    public int $accountId = 0;
    public bool $defaultNegative = false;
    public string $routeName = '';

    public string $label = '';
    public bool $negative_flow = false;
    public int $certainty = 100;

    public bool $modalMode = false;

    public function mount()
    {
        $account = BankAccount::findOrFail($this->accountId);

        if ($this->flowId) {
            $flow = AccountFlow::find($this->flowId);

            $this->label = $flow->label;
            $this->negative_flow = $flow->negative_flow;
            $this->certainty = $flow->certainty;
        } else {
            $this->negative_flow = $this->defaultNegative;
        }
    }

    protected function rules()
    {
        return [
            'label' => ['required', 'min:3'],
            'negative_flow' => ['required'],
            'certainty' => ['required', 'integer', 'min:5', 'max:500']
        ];
    }

    public function store()
    {
        $validateValues = $this->validate();

        $flow = $this->flowId ? AccountFlow::find($this->flowId) : new AccountFlow();
        $flow->label = $validateValues['label'];
        $flow->negative_flow = $validateValues['negative_flow'];
        $flow->certainty = $validateValues['certainty'];

        $account = null;

        if ($this->accountId) {
            $account = BankAccount::findOrFail($this->accountId);
            $flow->account_id = $account->id;
        }

        $flow->save();

        if ($this->modalMode) {
            $this->emit('reloadRevenueTable');
        } else {
            return redirect("business/".$account->business->id."/accounts");
        }
    }

    public function render()
    {
        return view('livewire.add-edit-flow');
    }
}
