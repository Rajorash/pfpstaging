<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use App\Models\AllocationPercentage;
use App\Models\BankAccount;

class AllocationCalculatorController extends Controller
{
    use GettersTrait;

    public $mappedAccounts;
    public $checkPercentagesSet;
    public $hideSalesTaxRows;
    public ?Business $business = null;
    public $revenue;
    public $netCashReceipts;
    public $realRevenue;
    public $postrealPercentageSum;
    public $allocationSum = 0;
    public $checksum = 0;
    public $json_type = false;

    /**
     * render the view of the allocation calculator
     *
     * @return View
     */
    public function index(): View
    {
        return $this->getView();
    }

    /**
     * render the view of the allocation calculator
     *
     * @return View
     */
    public function indexWithId(Business $business): View
    {
        return $this->getView($business);
    }

    public function calculatBusiness(Request $request )
    {
           $this->revenue  =   $request->revenueinput;
           $this->business = $this->getBusiness($request->businessId); 
           $this->json_type = true;
            return  $this->getView($this->business);
    }

    /**
     * @param  Business|null  $business
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function getView(Business $currentbusiness = null)
    {
        $viewable = $this->getViewableBusinesses();
        $this->business = $this->getBusiness(optional($currentbusiness)->id ?? $viewable->first()->id); 
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->netCashReceipts = $this->calculateNetCashReceipts();
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->realRevenue = $this->calculateRealRevenue();
        $this->mappedAccounts = $this->mapBusinessAccounts();
        $this->allocationSum = $this->calculateAllocationSum();
        $this->checkPercentagesSet = $this->checkPercentagesSet();
        $this->hideSalesTaxRows = $this->hideSalesTaxRows();
        $this->postrealPercentageSum = $this->calculatePercentageSum();
        $this->checksum = $this->allocationSum - $this->revenue;



        if($this->json_type){
            return  json_encode(["mappedAccounts"=>$this->mappedAccounts,'netCashReceipts'=>$this->netCashReceipts, 'realRevenue' => $this->realRevenue,'hideSalesTaxRows' => $this->hideSalesTaxRows , 'postrealPercentageSum' => $this->postrealPercentageSum, 'allocationSum' => $this->allocationSum, 'checksum' => $this->checksum ,"return" => true]);
        }else{
            return view('calculator.allocation-calculator', [
                'businesses' => $viewable,
                'business' => $currentbusiness ?? $viewable->first(),
                'businessId' => optional($currentbusiness)->id ?? $viewable->first()->id,
                'checkPercentagesSet' => $this->checkPercentagesSet,
                'mappedAccounts' => $this->mappedAccounts,
                'hideSalesTaxRows' => $this->hideSalesTaxRows,
                'netCashReceipts' => $this->netCashReceipts,
                'realRevenue' => $this->realRevenue,
                'postrealPercentageSum' => $this->postrealPercentageSum,
                'allocationSum' => $this->allocationSum,
                'checksum' => $this->checksum
            ]);
        }

       
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

    public function calculateNetCashReceipts(): float
    {
        // assumes only a single salestax account, will cause issues with multiple
        // advised by client that this should not happen
        $salestaxPercent = ($this->mappedAccounts['salestax'][0]['percent'] / 100);

        return round($this->revenue / ($salestaxPercent + 1), 4);
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

    private function getAllocationPercentage($current_phase, $account_id)
    {
        return AllocationPercentage::where([
            ['phase_id', $current_phase],
            ['bank_account_id', $account_id]
        ])->value('percent');
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

        // echo $type."sai1" ,$this->revenue."sai12", $this->netCashReceipts."sai3", $percent."sai4", $this->realRevenue."sai5", "pulkit value";

        return round($allocationValue, 4);
    }


    private function getBusiness($selectedBusinessId)
    {
        return Business::find($selectedBusinessId);
    }

    public function checkPercentagesSet():bool
    {

        $totalPercentagesValue = array_sum(data_get($this->mappedAccounts, '*.*.percent', 0));

        return $totalPercentagesValue > 0;

    }

    /**
     * returns a collection of viewable businesses based on the authorised
     * logged in user.
     *
     * @return Collection
     */
    private function getViewableBusinesses(): Collection
    {
        $businesses = $this->getBusinessAll();

        return $businesses->filter(function ($business) {
            return Auth::user()->can('view', $business);
        })->values();
    }
}
