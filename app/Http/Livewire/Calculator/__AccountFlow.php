<?php

namespace App\Http\Livewire\Calculator;

use App\Models\AccountFlow as Flow;
use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Carbon\Carbon;

class AccountFlow extends Component
{
    public $flowId;
    public Flow $flow;
    public $allocation;
    public $date;
    public $datesRange;
    public $account_id;
    public $accountIdToCall;
    public $amount;
    public $phase_id = 1;

    public function mount($flowId, $date, $datesRange)
    {
        $this->flowId = $flowId;
        $this->flow = $this->getFlow($flowId);
        $this->account_id = $this->flow->account_id;
        $this->date = $date;
        $this->phase_id = $this->flow->account->business->getPhaseIdByDate($date);
        $cdate = Carbon::parse($date);
        $this->datesRange = array_filter(collect($datesRange)->map(function ($item) use ($cdate) {
            if ($item >= $cdate) {
                return $item->toDateString();
            }
        })->toArray());
        $this->allocation = self::getAllocation($flowId, $date);
        $this->amount = $this->allocation
            ? number_format($this->allocation->amount, 0, '.', '')
            : 0;
    }

    private function getFlow($flowId)
    {
        $key = 'getFlow_'.$flowId;

        $getFlow = Cache::get($key);

        if ($getFlow === null) {
            $getFlow = Flow::find($flowId);

            Cache::put($key, $getFlow);
        }

        return $getFlow;
    }

    public function render()
    {
        return view('livewire.calculator.account-flow');
    }

    private function getAllocation($flowId, $date)
    {
        $key = 'getAllocation_'.$flowId.'_'.$date;

        $getAllocation = Cache::get($key);

        if ($getAllocation === null) {
            $getAllocation = Allocation::where([
                ['allocatable_type', '=', 'App\Models\AccountFlow'],
                ['allocatable_id', '=', $flowId],
                ['allocation_date', '=', $date]
            ])->first();

            Cache::put($key, $getAllocation);
        }

        return $getAllocation ?? null;
    }

    public function updatedAmount()
    {
        $valid = $this->validate([
            'amount' => 'present|numeric|nullable'
        ]);

        if (!$valid['amount'] && is_object($this->allocation)) {
            $this->allocation->delete();
            return;
        }

        if (is_numeric($this->amount)) {
            $this->store();
            $this->emit('updateAccountTotal:account_total_'.$this->account_id.'_'.substr($this->date, 0, 10),
                [
                    'salestax_id' => $this->flow->account->business->getAccountIdByType('salestax'),
                    'pretotal_id' => $this->flow->account->business->getAccountIdByType('pretotal'),
                    'prereal_ids' => $this->flow->account->business->getAllAccountIdsByType('prereal'),
                    'postreal_ids' => $this->flow->account->business->getAllAccountIdsByType('postreal'),
                    'dates_range' => $this->datesRange
                ]
            );
        }
    }

    public function store()
    {
        $this->validate([
            'amount' => 'numeric|nullable'
        ]);

        $data = array(
            'amount' => $this->amount
        );

        // if a valid amount is entered, store it in the database
        $allocation = Allocation::updateOrCreate([
            'allocatable_id' => $this->flowId,
            'allocatable_type' => 'App\Models\AccountFlow',
            'allocation_date' => $this->date,
            'phase_id' => $this->phase_id,
        ], $data);
        // if the number entered is 0, delete the allocation

        // session()->flash('message', $this->skill_id ? 'Skill updated successfully.' : 'Skill created successfully.');
        // session()->flash('messageClass', 'success');
        // $this->closeModal();
        // $this->resetInputFields();

    }
}
