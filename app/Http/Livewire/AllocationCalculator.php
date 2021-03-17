<?php

namespace App\Http\Livewire;

use App\Models\Business;
use App\Models\AllocationPercentage;
use Livewire\Component;

class AllocationCalculator extends Component
{

    public $revenue;
    public $netCashReceipts;
    public $realRevenue;
    public $allocationSum;
    public $checksum;
    public $postrealPercentageSum;

    public $selectOptions;
    public $selectedBusinessId;
    public Business $business;

    public $mappedAccounts;

    public function mount()
    {
        $this->selectOptions = $this->mapBusinessSelect();
        $this->business = auth()->user()->businesses->first();
        $this->selectedBusinessId = $this->business->id;
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->postrealPercentageSum = $this->getPercentageSum();

        $this->fill([
            'allocationSum' => 0,
            'checksum' => 0
        ]);

    }

    public function updatedSelectedBusinessId($new_value)
    {
        // $this->selectedBusinessId = $business_id;
        $this->business = Business::find($new_value);
        $refresh;

    }

    public function updatedRevenue($new_value)
    {
        $this->revenue = is_numeric($new_value) ? $new_value : 0;
        $refresh;

    }

    public function render()
    {
        $this->business = Business::find($this->selectedBusinessId) ?? auth()->user()->businesses->first();
        $this->netCashReceipts = $this->calculateNetCashReceipts();
        // calculate first to get base for realrevenue
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->realRevenue = $this->calculateRealRevenue();
        // refresh after realrevenue
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->allocationSum = $this->calculateAllocationSum();
        $this->postrealPercentageSum = $this->getPercentageSum();
        return view('livewire.allocation-calculator');
    }

    public function mapBusinessSelect()
    {
        $businesses = auth()->user()->businesses;

        return $businesses->keyBy('id')->map(
            function ($business) {
                return $business->name;
            }
        );
    }

    public function mapBusinessAccounts()
    {
        $current_phase = $this->business->getPhaseIdByDate(today());
        return $this->business->accounts->mapToGroups(
            function($account) use ($current_phase)
            {
                $percent = AllocationPercentage::where([
                    ['phase_id', $current_phase],['bank_account_id', $account->id]
                ])->value('percent') ?? 0;

                $value = $this->calculateAllocation($account->type, $percent);

                return [$account->type => [
                    'id' => $account['id'],
                    'name' => $account['name'],
                    'percent' => $percent,
                    'value' => $value,
                ]];
            }
        )->toArray();
    }

    public function calculateNetCashReceipts()
    {
        // assumes only a single salestax account, will cause issues with multiple
        $salestaxPercent = ($this->mappedAccounts['salestax'][0]['percent'] / 100);

        return round($this->revenue / ($salestaxPercent + 1), 4);

    }

    public function calculateRealRevenue()
    {
        // $prerealSum = collect($this->mappedAccounts['prereal'])->sum('value');
        $prerealSum = array_sum(data_get($this->mappedAccounts, 'prereal.*.value'));
        $result = $this->netCashReceipts - $prerealSum;

        return round($result, 4);

    }

    public function calculateAllocation($type, $percent)
    {
        $allocationValue = 0;

        if ($type == 'salestax') {
            $allocationValue = ($this->revenue - $this->netCashReceipts);
        }

        if ($type == 'pretotal') {
            $allocationValue = $this->netCashReceipts * ($percent / 100);
        }

        if ($type == 'prereal') {
            $allocationValue = $this->netCashReceipts * ($percent / 100);
        }

        if ($type == 'postreal') {
            $allocationValue = $this->realRevenue * ($percent / 100);
        }

        return round($allocationValue, 4);
    }

    public function calculateAllocationSum()
    {
        // cycle through the mapped accounts and total the value
        $account_values = data_get($this->mappedAccounts, '*.*.value');

        return array_sum($account_values);
    }

    public function getPercentageSum()
    {
        return array_sum(data_get($this->mappedAccounts, 'postreal.*.percent'));
    }
}
