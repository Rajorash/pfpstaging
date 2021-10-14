<?php

namespace App\Http\Livewire;

use App\Models\BankAccount;
use App\Models\Business;
use App\Models\AllocationPercentage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class AllocationCalculator extends Component
{

    public $revenue;
    public $netCashReceipts;
    public $realRevenue;
    public $postrealPercentageSum;
    public $allocationSum;
    public $checksum;

    public $selectOptions;
    public $selectedBusinessId;
    public ?Business $business = null;
    public $businesses;
    public $businessId;

    public $mappedAccounts;

    public function mount()
    {
        $this->selectOptions = $this->mapBusinessSelect();
        $this->fill([
            'allocationSum' => 0,
            'checksum' => 0
        ]);

        if ($this->businessId) {
            $this->selectedBusinessId = $this->businessId;
        }
    }

    /**
     * @param $businessId
     */
    public function updatedSelectedBusinessId($businessId)
    {
        return redirect()->route('allocation-calculator-with-id', ['business' => $businessId]);
    }

    public function updatedRevenue($new_value)
    {
        $this->revenue = is_numeric($new_value) ? $new_value : 0;
        $refresh;
    }

    private function getBusiness($selectedBusinessId)
    {
        return Business::find($selectedBusinessId);
    }

    public function render()
    {
        $this->business = $this->getBusiness($this->selectedBusinessId) ?? $this->businesses->first();
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->netCashReceipts = $this->calculateNetCashReceipts();

        // calculate first to get base for realrevenue
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->realRevenue = $this->calculateRealRevenue();

        // refresh after realrevenue
        $this->mappedAccounts = $this->mapBusinessAccounts();

        // calculate checksums
        $this->allocationSum = $this->calculateAllocationSum();
        $this->postrealPercentageSum = $this->calculatePercentageSum();
        $this->checksum = $this->allocationSum - $this->revenue;

        return view('livewire.allocation-calculator');
    }

    private function getAllocationPercentage($current_phase, $account_id)
    {
        return AllocationPercentage::where([
            ['phase_id', $current_phase],
            ['bank_account_id', $account_id]
        ])->value('percent');
    }

    public function mapBusinessAccounts(): array
    {
        $current_phase = $this->business->current_phase;

        return $this->business->accounts->mapToGroups(
            function ($account) use ($current_phase) {
//                $percent = AllocationPercentage::where([
//                        ['phase_id', $current_phase],
//                        ['bank_account_id', $account->id]
//                    ])->value('percent') ?? 0;

                $percent = $this->getAllocationPercentage($current_phase, $account->id);

                $value = $this->calculateAllocation($account->type, $percent);

                return [
                    $account->type => [
                        'id' => $account['id'],
                        'name' => $account['name'],
                        'percent' => $percent,
                        'value' => $value,
                    ]
                ];
            }
        )->toArray();
    }

    public function mapBusinessSelect()
    {
        return $this->businesses->keyBy('id')->map(
            function ($business) {
                return $business->name;
            }
        );
    }

    public function calculateNetCashReceipts(): float
    {
        // assumes only a single salestax account, will cause issues with multiple
        // advised by client that this should not happen
        $salestaxPercent = ($this->mappedAccounts['salestax'][0]['percent'] / 100);

        return round($this->revenue / ($salestaxPercent + 1), 4);
    }

    public function calculateRealRevenue(): float
    {
        // $prerealSum = collect($this->mappedAccounts['prereal'])->sum('value');
        $prerealSum = 0;

        // make sure to account for NULL if no prereal accounts exist, array_sum
        // will throw an error.
        $prerealData = data_get($this->mappedAccounts, 'prereal.*.value');

        if ($prerealData) {
            $prerealSum = array_sum($prerealData);
        }

        $result = $this->netCashReceipts - $prerealSum;

        return round($result, 4);
    }

    public function calculateAllocation($type, $percent): float
    {
        $allocationValue = 0;

        if ($type == BankAccount::ACCOUNT_TYPE_SALESTAX) {
            $allocationValue = ($this->revenue - $this->netCashReceipts);
        }

        if ($type == BankAccount::ACCOUNT_TYPE_PRETOTAL) {
            $allocationValue = $this->netCashReceipts * ($percent / 100);
        }

        if ($type == BankAccount::ACCOUNT_TYPE_PREREAL) {
            $allocationValue = $this->netCashReceipts * ($percent / 100);
        }

        if ($type == BankAccount::ACCOUNT_TYPE_POSTREAL) {
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

    public function calculatePercentageSum()
    {
        return array_sum(
            data_get(
                $this->mappedAccounts, 'postreal.*.percent'
            )
        );
    }

    public function getBusinessProperty()
    {
        return $this->selectedBusinessId
            ? $this->businesses->first()
            : $this->getBusiness($this->selectedBusinessId);
//            : Business::find($this->selectedBusinessId);
    }

    /**
     * utility function to hide the sales tax and net cash receipts rows if no
     * salestax values are set or they are <= 0
     *
     * @return bool
     */
    public function hideSalesTaxRows(): bool
    {
        if (empty(data_get($this->mappedAccounts, 'salestax'))) {
            return true;
        }

        if (array_sum(data_get($this->mappedAccounts, 'salestax.*.percent')) <= 0) {
            return true;
        }

        return false;
    }


    public function checkPercentagesSet():bool
    {

        $totalPercentagesValue = array_sum(data_get($this->mappedAccounts, '*.*.percent', 0));

        return $totalPercentagesValue > 0;

    }
}
