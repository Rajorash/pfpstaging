<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\AllocationPercentage;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Illuminate\Support\Facades\DB;

class BusinessAllocationsController extends Controller
{
    protected $business = null;
    private array $phases = [];
    private array $percentages = [];
    private array $incomeByPeriod = [];
    private array $updated_today = [];
    private array $rawData;
    private int $complete;

    private array $accountFlowCache = [];
    private array $bankAccountCache = [];
    private array $previousNonZeroValueCache = [];
    public array $accountsSubTypes = [];
    protected int $defaultCurrentRangeValue = 365;

    public const RANGE_DAILY = 1;
    public const RANGE_WEEKLY = 7;
    public const RANGE_FORTNIGHTLY = 14;
    public const RANGE_MONTHLY = 31;
    public const RANGE_QUARTERLY = 93;
    public const RANGE_YEARLY = 365;

    protected int $periodInterval = 1;
    public const PROJECTION_MODE_EXPENSE = 'expense';
    public const PROJECTION_MODE_FORECAST = 'forecast';
    protected string $projectionMode = self::PROJECTION_MODE_EXPENSE;
    protected int $forecastRowsPerPeriod = 10;
    protected string $tableAttributes = '';

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $businessId = $request->business ?? null;
        session(['businessId' => $businessId]);
        $this->business = Business::findOrFail($businessId);
        $this->authorize('view', $this->business);

        $today = \JamesMills\LaravelTimezone\Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');

        $checkUpdate = new BankAccount;
        $update_bal_date = '';
        $accounts = $this->business->accounts;
        foreach ($accounts as $key => $account) {
             $updated_today[$key] = $checkUpdate->dateOfUpdateBalanceEntry($account->id);
             if($updated_today[$key] !== ''){
                $update_bal_date = $updated_today[$key];
             }
        }
       
        // $updated_today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d');
//        $maxDate = $this->business->rollout()->max('end_date');
        $maxDate = Carbon::parse($today)->addYears(5)->format('Y-m-d');
        $minDate = $this->business->rollout()->min('end_date');
        $startDate = session()->get('startDate_'.$this->business->id,
            Carbon::parse($today)->addDays(-4)->format('Y-m-d'));

        return view('business.business-allocations', [
            'business' => $this->business,
            'rangeArray' => $this->getRangeArray(),
            'startDate' => $startDate,
            'minDate' => Carbon::parse($minDate)->subMonths(3)->format('Y-m-d'),
            'maxDate' => $maxDate,
            'updated_today' => $update_bal_date ? $update_bal_date : Timezone::convertToLocal(Carbon::now(), 'Y-m-d'),
            'currentRangeValue' => session()->get('rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue),
        ]);
    }

    public function updateDragRow(Request $request)
    {
        if($request->drop_accountId != null && $request->drop_flowId != null && $request->current_accountId != null && $request->current_flowId != null && $request->getAllFlowId != null){

            $dropUser = AccountFlow::where('account_id',$request->drop_accountId)->where('id',$request->drop_flowId)->update([ 'account_id' => $request->current_accountId]);

            $verifyAcountId =  $request->getAllFlowId;

            for($i = 0; $i < count($verifyAcountId); $i++ ){
                    $user = AccountFlow::where('account_id',$request->current_accountId)->where('id',$verifyAcountId[$i])->update([ 'flow_position' => $i + 1]);   
            }

            
                if($user){
                    return  json_encode(["result"=>"update sucessfully!","return" => true]);
                }

        }
    }

    public function updateData(Request $request): \Illuminate\Http\JsonResponse
    {    
        
        $response = [
            'error' => [],
            'html' => [],
        ];

        
        $tableData = [
            BankAccount::ACCOUNT_TYPE_REVENUE => [],
            BankAccount::ACCOUNT_TYPE_PRETOTAL => [],
            BankAccount::ACCOUNT_TYPE_SALESTAX => [],
            BankAccount::ACCOUNT_TYPE_PREREAL => [],
            BankAccount::ACCOUNT_TYPE_POSTREAL => []
        ];

        $businessId = $request->businessId ?? null;
        $this->business = Business::where('id', $businessId)
            ->with([
                'accounts',
            ])
            ->first();

        $returnType = $request->returnType ?? 'html';
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        $todayShort = Timezone::convertToLocal(Carbon::now(), 'Y-m-d').' 00:00:00';

        if ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            $startDate = $request->startDate ?? Carbon::parse($today)->format('Y-m-d');
        } else {
            $startDate = $request->startDate ?? Carbon::parse($today)->addDays(-4)->format('Y-m-d');
        }

//        $startDate = $request->startDate ?? session()->get('startDate_'.$this->business->id,
//                Timezone::convertToLocal(Carbon::now(), 'Y-m-d'));

        $rangeValue = $request->rangeValue ?? $this->defaultCurrentRangeValue;

    

        if ($this->projectionMode == 'expense') {
            $this->complete = $rangeValue;
        } elseif ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            $this->complete = $this->periodInterval * $this->forecastRowsPerPeriod;
        }

        $endDate = Carbon::parse($startDate)->addDays($this->complete - 1)->format('Y-m-d');

        $period = CarbonPeriod::create($startDate, $endDate);

        $this->phases = $this->business->getPhasesIdByPeriod($period);

        

        //update value of cell
        $cellId = $request->cellId ?? null;
        $cellValue = $request->cellValue ?? null;
        $updatedAccountId = null;
        if ($cellId && $cellValue !== null) {
            $returnCellAttributes = $this->storeCellValue($cellId, floatVal($cellValue));
            $updatedAccountId = optional(optional(AccountFlow::find($returnCellAttributes['accountId']))->account)->id;
        }

        $this->rawData = $this->getRawData($this->business->id, $startDate, $endDate, $updatedAccountId);
        $this->percentages = $this->getPercentagesByPhasesId(
            $this->business->id,
            array_unique(array_values($this->phases))
        );

        if (empty($this->accountsSubTypes)) {
            $this->accountsSubTypes = $this->getAccountsSubTypes();
        }

        $accounts = $this->business->accounts;
        foreach ($accounts as $account) {

            // $checkUniqueCatId  = DB::table('account_flows')->where('account_id', $account->id)->distinct('cat_id')->pluck('cat_id')->toArray();

            // $checkUniqueCat[$account->id]['name'] = DB::table('account_categories')->select('category_name')->whereIn('id',$checkUniqueCatId)->get();

            // dd($checkUniqueCat);

            //during return json - recalculate only Account which related to changed cell
            if ($returnType != 'html' && $updatedAccountId && $updatedAccountId != $account->id) {
                continue;
            }

            $accountAllData = $account->toArray();

            //filter only data between $startDate and $endDate
            if (isset($accountAllData['allocations'])) {
                $accountAllData['allocations'] = array_filter(
                    $accountAllData['allocations'],
                    function ($element) use ($startDate, $endDate) {
                        return Carbon::parse($element['allocation_date'])->betweenIncluded($startDate, $endDate);
                    }
                );
            }

            // $allAccounts = AccountFlow::where('account_id', $account->id)->get();

            $tableData[$account->type][$account->id] = $accountAllData;

            if ($account->type == BankAccount::ACCOUNT_TYPE_REVENUE) {
                session(['acccountTransferId' => $account->id]);
                $this->incomeByPeriod = $account->getAdjustedFlowsTotalByDatePeriod($account->id,$startDate, $endDate);
            }else{
                $accccid = session('acccountTransferId') ? session('acccountTransferId') : $account->id;
                $this->incomeByPeriod = $account->getAdjustedFlowsTotalByDatePeriod($accccid,$startDate, $endDate);
            }

            $tableData[$account->type][$account->id]['total_db'] =
                $account->getAdjustedFlowsTotalByDatePeriod($account->id,$startDate, $endDate);

            if (array_key_exists('flows', $tableData[$account->type][$account->id])) {
                //reorder flows data
                $newFlows = [];
                foreach ($tableData[$account->type][$account->id]['flows'] as $flowArray) {
                        $newFlows[$flowArray['id']] = $flowArray;
                }
                $tableData[$account->type][$account->id]['flows'] = $newFlows;
            }

            $tableData[$account->type][$account->id] = $this->fillMissingDateValue(
                $period,
                $tableData[$account->type][$account->id],
                $startDate
            );
        }

        //reset period for Forecast
        if ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            switch ($rangeValue) {
                case self::RANGE_DAILY:
                    //left as is
                    break;
                case self::RANGE_WEEKLY:
                    $period = CarbonPeriod::since($startDate)->week()->until($endDate);
                    break;
                case self::RANGE_FORTNIGHTLY:
                    $period = CarbonPeriod::since($startDate)->weeks(2)->until($endDate);
                    break;
                case self::RANGE_MONTHLY:
                    $period = CarbonPeriod::since($startDate)->month()->until($endDate);
                    break;
                case self::RANGE_QUARTERLY:
                    $period = CarbonPeriod::since($startDate)->months(3)->until($endDate);
                    break;
                 case self::RANGE_YEARLT:
                    $period = CarbonPeriod::since($startDate)->months(6)->until($endDate);
                    break;
            }

       

            $tableData = $this->optimizationTableData($tableData, $period);
        }

        // dd($tableData);
        $periodDates = [];
        foreach ($period as $date) {
            $periodDates[] = $date->format('Y-m-d');
        }

        if ($returnType == 'html') {
            $response['html'] = view('business.business-allocations-table')
                ->with([
                    'business' => $this->business,
                    'rangeArray' => $this->getRangeArray(),
                    'startDate' => $startDate,
                    'period' => $period,
                    'periodDates' => $periodDates,
                    'tableData' => $tableData,
                    'rangeValue' => $rangeValue,
                    'accountsSubTypes' => $this->accountsSubTypes,
                    'projectionMode' => $this->projectionMode,
                    'tableAttributes' => $this->tableAttributes,
                    'today' => $today,
                    'todayShort' => $todayShort
                ])->render();

            return response()->json($response);
        } else {
            return response()->json([
                    'error' => $response['error'],
                    'data' => $this->convertTableDataToFlatArrayForJSONUpdate($tableData)
                ]
            );
        }
    }


    protected function getAccountsSubTypes(): array
    {
        return [
            '_dates' => [
                'title' => '_self',
                'class_tr' => 'bg-account',
                'class_th' => 'pl-2',
            ],
            'transfer' => [
                'title' => __('Transfer In'),
                'class_tr' => 'bg-readonly',
                'class_th' => 'pl-4',
            ],
            'total' => [
                'title' => __('Flow Total'),
                'class_tr' => 'bg-readonly',
                'class_th' => 'pl-4',
            ],
            'sub_total' => [
                'title' => __('Others Flow Total'),
                'class_tr' => 'bg-readonly',
                'class_th' => 'pl-4',
            ]
        ];
    }

    /**
     * @param  CarbonPeriod  $period
     * @param  array  $data
     * @param  string  $startDate
     * @return array
     */
    protected function fillMissingDateValue(CarbonPeriod $period, array $data, string $startDate): array
    {
        $id = $data['id'];
        // $id = 60;

        switch ($data['type']) {
            case BankAccount::ACCOUNT_TYPE_REVENUE:
                foreach ($period as $date) {
                    $data['total'][$date->format('Y-m-d')] = $data['total'][$date->format('Y-m-d')] ?? 0;
                    $data['sub_total'][$date->format('Y-m-d')] = $data['sub_total'][$date->format('Y-m-d')] ?? 0;
                }
                break;

            case BankAccount::ACCOUNT_TYPE_PRETOTAL:
                $flows = [];
                foreach ($period as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $currentDateTime = $date->format('Y-m-d').' 00:00:00';
                    $phaseId = $this->phases[$currentDate];
                    $percents = $this->percentages[$phaseId];
                    $salesTax = data_get($percents, 'salestax');
                    $salesTax = count($salesTax) > 0 ? $salesTax[key($salesTax)] : null;
                    $income = $this->incomeByPeriod[$currentDate] ?? 0;
                    $nsp = ($income && $salesTax) ? $income / ($salesTax / 100 + 1) : 0;

                    $percent = $this->percentages[$phaseId][BankAccount::ACCOUNT_TYPE_PRETOTAL][$id];
                    $data['transfer'][$currentDate] = (is_numeric($percent))
                        ? round($nsp * (floatval($percent) / 100), 4)
                        : 0;

                    $this->fillFlowsAndData($flows, $data, $id, $currentDate, $currentDateTime, $startDate);
                }
                break;

            case BankAccount::ACCOUNT_TYPE_SALESTAX:
                foreach ($period as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $currentDateTime = $date->format('Y-m-d').' 00:00:00';
                    $phaseId = $this->phases[$currentDate];
                    $percents = $this->percentages[$phaseId];

                    $income = $this->incomeByPeriod[$currentDate] ?? 0;
                    $data['transfer'][$currentDate] = $this->calculateSalestaxTransfer($id, $income, $percents);

                    $this->fillFlowsAndData($flows, $data, $id, $currentDate, $currentDateTime, $startDate);

                }
                break;

            case BankAccount::ACCOUNT_TYPE_PREREAL:
                $flows = [];
                foreach ($period as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $currentDateTime = $date->format('Y-m-d').' 00:00:00';
                    $phaseId = $this->phases[$currentDate];
                    $percents = $this->percentages[$phaseId];
                    $income = $this->incomeByPeriod[$currentDate] ?? 0;
                    $prereal = $this->getPrePrereal($income, $percents);
                    $percent = $this->percentages[$phaseId][BankAccount::ACCOUNT_TYPE_PREREAL][$id];

                    $data['transfer'][$currentDate] = (is_numeric($percent))
                        ? round($prereal * ($percents[BankAccount::ACCOUNT_TYPE_PREREAL][$id] / 100), 4)
                        : 0;

                    $this->fillFlowsAndData($flows, $data, $id, $currentDate, $currentDateTime, $startDate);

                }
                break;

            case BankAccount::ACCOUNT_TYPE_POSTREAL:
                $flows = [];
                foreach ($period as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $currentDateTime = $date->format('Y-m-d').' 00:00:00';
                    $phaseId = $this->phases[$currentDate];
                    $percents = $this->percentages[$phaseId];
                    $income = $this->incomeByPeriod[$currentDate] ?? 0;
                    $prereal = $this->getPrePrereal($income, $percents);
                    $percent = $this->percentages[$phaseId][BankAccount::ACCOUNT_TYPE_POSTREAL][$data['id']];
                    $prereal_percents = key_exists(BankAccount::ACCOUNT_TYPE_PREREAL, $percents)
                        ? array_sum($percents[BankAccount::ACCOUNT_TYPE_PREREAL])
                        : 0;

                    // Real Revenue = $prereal - $prereal * ($prereal_percents / 100)
                    $data['transfer'][$currentDate] = (is_numeric($percent))
                        ? round(
                            ($prereal - $prereal * ($prereal_percents / 100))
                            * ($percents[BankAccount::ACCOUNT_TYPE_POSTREAL][$id] / 100),
                            4
                        )
                        : 0;

                    $this->fillFlowsAndData($flows, $data, $id, $currentDate, $currentDateTime, $startDate);
                }
                break;
        }

        return $data;
    }

    /**
     * @param  int  $businessId
     * @param  array  $phasesIdArray
     * @return array
     */
    protected function getPercentagesByPhasesId(int $businessId, array $phasesIdArray): array
    {
        $percentages = [];

        foreach ($phasesIdArray as $phaseId) {
            $percentages[$phaseId] = BankAccount::where('business_id', $businessId)
                ->with('percentages', function ($query) use ($phaseId) {
                    return $query->where('phase_id', $phaseId);
                })
                ->get()
                ->mapToGroups(function ($item, $key) {
                    return [
                        $item->type => [
                            'id' => $item->id,
                            'val' => count($item->percentages) ? $item->percentages[0]->percent : null
                        ]
                    ];
                })->map(function ($a_item) {
                    return array_column(collect($a_item)->toArray(), 'val', 'id');
                })->toArray();
        }

        return $percentages;
    }

    /**
     * @param  int  $businessId
     * @param  string  $dateFrom
     * @param  string  $dateTo
     * @param  null  $updatedAccountId
     * @return array
     */
    private function getRawData(int $businessId, string $dateFrom, string $dateTo, $updatedAccountId = null): array
    {
        $raw = BankAccount::where('business_id', $businessId)
            ->with('flows.allocations', function ($query) use ($dateFrom, $dateTo) {
                return $query->where('allocation_date', '>=', $dateFrom)
                    ->where('allocation_date', '<=', $dateTo);
            })
            ->with('allocations', function ($query) use ($dateFrom, $dateTo) {
                return $query->where('allocation_date', '>=', $dateFrom)
                    ->where('allocation_date', '<=', $dateTo);
            })
            ->get()
            ->map(function ($item) use ($updatedAccountId) {
                if ($updatedAccountId && $item->id != $updatedAccountId) {
                    //leave as is
                } else {
                    $flows = [];
                    foreach ($item->flows as $flow) {
                        $flows += [
                            $flow->id => $flow->allocations->pluck('amount', 'allocation_date')->toArray()
                                + ['negative' => (bool) $flow->negative_flow]
                                + ['name' => $flow->label]
                                + ['certainty' => $flow->certainty]
                        ];
                    }

                    $amounts = $item->allocations->pluck('amount', 'allocation_date')->toArray();
                    $manuals = $item->allocations->pluck('manual_entry', 'allocation_date')->toArray();

                    $item->account_values = [
                        $item->id => array_merge_recursive($amounts, $manuals) + $flows
                    ];
                }

                return $item;
            })
            ->all();

        $result = [];

       
        foreach ($raw as $account_item) {
            if (isset($account_item->account_values)) {
                foreach ($account_item->account_values as $key => $value) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param  int  $accountId
     * @param  string  $dateFrom
     * @return mixed|null
     */
    private function getPreviousNonZeroValue(int $accountId, string $dateFrom)
    {
        $key = $accountId.'_'.$dateFrom;

        if (!key_exists($key, $this->previousNonZeroValueCache)) {
            $result = BankAccount::where('id', $accountId)
                ->with('allocations', function ($query) use ($dateFrom) {
                    return $query->where('allocation_date', '<', $dateFrom)
                        ->orderBy('allocation_date', 'desc');
                })
                ->get()//;
                ->map(function ($item) {
                    return $item->allocations->slice(0, 1)->pluck(['amount']);
                })->pop()->toArray();

                // print_r($result);die("cc");

            $this->previousNonZeroValueCache[$key] = (count($result) > 0) ? $result[0] : 0;
        }

        return $this->previousNonZeroValueCache[$key];
    }

    /**
     * @param  string  $type
     * @param  int  $accountId
     * @param  float  $amount
     * @param  string  $date
     * @param  bool  $manualEntry
     */
    private function store(string $type, int $accountId, float $amount, string $date, bool $manualEntry = false)
    {
        $phaseId = $this->phases[$date];

        if ($type == 'flow') {
            $account = $this->getFlowAccount($accountId);
        } else {
            $account = $this->getBankAccount($accountId);
        }

        $account->allocate($amount, $date, $phaseId, $manualEntry, false);
    }

    /**
     * @param  int  $accountId
     * @return AccountFlow|AccountFlow[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    private function getFlowAccount(int $accountId)
    {
        if (!array_key_exists($accountId, $this->accountFlowCache)) {
            $this->accountFlowCache[$accountId] = AccountFlow::find($accountId);
        }

        return $this->accountFlowCache[$accountId];
    }

    /**
     * @param  int  $accountId
     * @return BankAccount|BankAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    private function getBankAccount(int $accountId)
    {
        if (!array_key_exists($accountId, $this->bankAccountCache)) {
            $this->bankAccountCache[$accountId] = BankAccount::find($accountId);
        }

        return $this->bankAccountCache[$accountId];
    }

    /**
     * @param  int  $id
     * @param  float  $income
     * @param  array  $percents
     * @return float|int
     */
    private function calculateSalestaxTransfer(int $id, float $income, array $percents)
    {
        $transfer_amount = 0;

        if ($income > 0) {
            $transfer_amount = round(
                $income - $income / ($percents[BankAccount::ACCOUNT_TYPE_SALESTAX][$id] / 100 + 1),
                4);
        }

        return $transfer_amount;
    }

    /**
     * @param  array  $data
     * @param  string  $date
     * @return bool
     */
    private function hasManualEntry(array $data, string $date): bool
    {
        return isset($data['manual'][$date]);
    }

    /**
     * @param  float  $income
     * @param  array  $percents
     * @return float|int
     */
    private function getPrePrereal(float $income, array $percents)
    {
        $salestax = data_get($percents, 'salestax');
        $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
        $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;

        $pretotal = data_get($percents, 'pretotal');
        $pretotal = isset($pretotal) && count($pretotal) > 0 ? $pretotal[key($pretotal)] : null;
        $pretotal_amt = (is_numeric($pretotal))
            ? round($nsp * ($pretotal / 100), 4)
            : 0;

        return $nsp - $pretotal_amt;
    }

    /**
     * @param  array|null  $flows
     * @param  array  $data
     * @param  int  $id
     * @param  string  $currentDate
     * @param  string  $currentDateTime
     * @param  string  $startDate
     */
    private function fillFlowsAndData(
        ?array &$flows,
        array &$data,
        int $id,
        string $currentDate,
        string $currentDateTime,
        string $startDate
    ) {
        $key = null;

        $data['_dates'][$currentDate]
            = array_key_exists($currentDateTime, $this->rawData[$id])
            ? $this->rawData[$id][$currentDateTime]
            : 0;

        $flow_total = 0;
        $flow_sub_total = 0;

        //key as date string 'YYY-MM-DD 00:00:00'
        foreach ($this->rawData[$id] as $key => $value) {
            if (is_integer($key)) {
                $flows[$id][$key][$currentDate]
                    = array_key_exists($currentDateTime, $value)
                    ? $value[$currentDateTime]
                    : 0;
                $flow_total += ($value['negative'] ? -1 : 1)
                    * ($flows[$id][$key][$currentDate] * $value['certainty'] / 100);

                $flow_sub_total +=  ($flows[$id][$key][$currentDate]);
            } elseif ($key == $currentDateTime) {

                $data['manual'][$currentDate] = $value[1];
            }
        }

        $data['total'][$currentDate] = $flow_total;
        $data['sub_total'][$currentDate] = $flow_sub_total;

        if (
            array_key_exists($id, $flows)
            && array_key_exists($key, $flows[$id])
            && count($flows[$id][$key]) == $this->complete
        ) {
            foreach ($flows[$id] as $flowid => $flowDates) {
                $data['flows'][$flowid]['_dates'] = $flowDates;
            }
        }

        $actualValue = $flow_total + $data['transfer'][$currentDate];

        $previousDate = Carbon::parse($currentDate)->subDays(1)->format('Y-m-d');

        if (array_key_exists($previousDate, $data['_dates'])) {
            $actualValue += is_array($data['_dates'][$previousDate])
                ? $data['_dates'][$previousDate][0]
                : $data['_dates'][$previousDate];
        } else {
            // \DB::enableQueryLog(); 


            
            $previousNonZero = $this->getPreviousNonZeroValue($id, $startDate);
            if (is_numeric($previousNonZero)) {
                $actualValue += $previousNonZero;
            }
        // dd(\DB::getQueryLog()); 

        }

        $stored_value = is_array($data['_dates'][$currentDate])
            ? $data['_dates'][$currentDate][0]
            : $data['_dates'][$currentDate];

        if ($stored_value != $actualValue &&
            !$this->hasManualEntry($data, $currentDate)
        ) {
            $data['_dates'][$currentDate] = $actualValue;
            $this->store('account', $id, round($actualValue, 2), $currentDate);
        } else {
            $data['_dates'][$currentDate] = $stored_value;
        }
    }

    protected function getRangeArray(): array
    {
        return [
            self::RANGE_WEEKLY => 'Weekly',
            self::RANGE_FORTNIGHTLY => 'Fortnightly',
            self::RANGE_MONTHLY => 'Monthly'
        ];
    }

    /**
     * @param  array  $tableData
     * @return array
     */
    protected function convertTableDataToFlatArrayForJSONUpdate(array $tableData): array
    {
        $newTableData = [];

        foreach ($tableData as $accountsArray) {
            foreach ($accountsArray as $accountId => $accountData) {
                foreach ($this->accountsSubTypes as $subType => $subTypeArray) {
                    if (array_key_exists($subType, $accountData)) {
                        foreach ($accountData[$subType] as $currentDate => $value) {
                            $key = ($subType == '_dates')
                                ? 'account_'.$accountId.'_'.$currentDate
                                : $subType.'_'.$accountId.'_'.$currentDate;
                            $newTableData[$key] = number_format($value, 0, '.', '');
                        }
                    }
                }
                if (array_key_exists('flows', $accountData)) {
                    foreach ($accountData['flows'] as $flowId => $flowData) {
                        if (array_key_exists('_dates', $flowData)) {
                            foreach ($flowData['_dates'] as $currentDate => $value) {
                                $key = 'flow_'.$flowId.'_'.$currentDate;
                                $newTableData[$key] = number_format($value, 0, '.', '');
                            }
                        }
                    }
                }
            }
        }

        return $newTableData;
    }

    /**
     * @param  string  $cellId
     * @param  float  $amount
     * @return array
     */
    protected function storeCellValue(string $cellId, float $amount): array
    {
        $matches = [];
        preg_match('/(\w+)_(\d+)_(\d{4}-\d{2}-\d{2})/', $cellId, $matches);
        $type = $matches[1];
        $accountId = intval($matches[2]);
        $date = $matches[3];

        //allowed only for flows
        if ($type) {
            $this->store($type, $accountId, $amount, $date, ($type == 'account'));
        }

        return [
            'type' => $type,
            'accountId' => $accountId,
            'date' => $date
        ];
    }

    protected function optimizationTableData($tableData, $period)
    {
        return $tableData;
    }

    public function graph(Request $request)
    {
        $businessId = $request->business ?? null;
        $this->business = Business::findOrFail($businessId);
        return  view('business.graph')
        ->with([
            'business' => $this->business,
        ]);
    }


    public function getGraphData(Request $request)
    {    

        $response = [
            'error' => [],
            'html' => [],
        ];
        
        $tableData = [
            BankAccount::ACCOUNT_TYPE_REVENUE => [],
            BankAccount::ACCOUNT_TYPE_PRETOTAL => [],
            BankAccount::ACCOUNT_TYPE_SALESTAX => [],
            BankAccount::ACCOUNT_TYPE_PREREAL => [],
            BankAccount::ACCOUNT_TYPE_POSTREAL => []
        ];

        $businessId = $request->id ?? null;
        // $businessId=3;
        $this->business = Business::where('id', $businessId)
            ->with([
                'accounts',
            ])
            ->first();
        
        $returnType = $request->returnType ?? 'html';
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        $todayShort = Timezone::convertToLocal(Carbon::now(), 'Y-m-d').' 00:00:00';


        if ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            $startDate = $request->startDate ?? Carbon::parse($today)->format('Y-m-d');
        } else {
            $startDate = $request->startDate ?? Carbon::parse($today)->addDays(-4)->format('Y-m-d');
        }

        $rangeValue = $request->rangeValue ?? $this->defaultCurrentRangeValue;

        if ($this->projectionMode == 'expense') {
            $this->complete = $rangeValue;
        } elseif ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            $this->complete = $this->periodInterval * $this->forecastRowsPerPeriod;
        }
        
        $endDate = Carbon::parse($startDate)->addDays($this->complete - 1)->format('Y-m-d');

        $period = CarbonPeriod::create($startDate, $endDate);

        $this->phases = $this->business->getPhasesIdByPeriod($period);

        //update value of cell
        $cellId = $request->cellId ?? null;
        $cellValue = $request->cellValue ?? null;
        $updatedAccountId = null;
        if ($cellId && $cellValue !== null) {
            $returnCellAttributes = $this->storeCellValue($cellId, floatVal($cellValue));
            $updatedAccountId = optional(optional(AccountFlow::find($returnCellAttributes['accountId']))->account)->id;
        }
        
        $this->rawData = $this->getRawData($this->business->id, $startDate, $endDate, $updatedAccountId);

        $this->percentages = $this->getPercentagesByPhasesId(
            $this->business->id,
            array_unique(array_values($this->phases))
        );

        if (empty($this->accountsSubTypes)) {
            $this->accountsSubTypes = $this->getAccountsSubTypes();
        }

        $rangeValue = $request->rangeValue ?? $this->defaultCurrentRangeValue;

        if ($this->projectionMode == 'expense') {
            $this->complete = $rangeValue;
        } elseif ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            $this->complete = $this->periodInterval * $this->forecastRowsPerPeriod;
        }

        $endDate = Carbon::parse($startDate)->addDays($this->complete - 1)->format('Y-m-d');

        $period = CarbonPeriod::create($startDate, $endDate);

        $this->phases = $this->business->getPhasesIdByPeriod($period);

      

        //update value of cell
        $cellId = $request->cellId ?? null;
        $cellValue = $request->cellValue ?? null;
        $updatedAccountId = null;
        if ($cellId && $cellValue !== null) {
            $returnCellAttributes = $this->storeCellValue($cellId, floatVal($cellValue));
            $updatedAccountId = optional(optional(AccountFlow::find($returnCellAttributes['accountId']))->account)->id;
        }

        $this->rawData = $this->getRawData($this->business->id, $startDate, $endDate, $updatedAccountId);

    
        $this->percentages = $this->getPercentagesByPhasesId(
            $this->business->id,
            array_unique(array_values($this->phases))
        );

        if (empty($this->accountsSubTypes)) {
            $this->accountsSubTypes = $this->getAccountsSubTypes();
        }

        $accounts = $this->business->accounts;

        foreach ($accounts as $account) {
            if ($returnType != 'html' && $updatedAccountId && $updatedAccountId != $account->id) {
                continue;
            }
           

            $accountAllData = $account->toArray();

            //filter only data between $startDate and $endDate
            if (isset($accountAllData['allocations'])) {
                $accountAllData['allocations'] = array_filter(
                    $accountAllData['allocations'],
                    function ($element) use ($startDate, $endDate) {
                        return Carbon::parse($element['allocation_date'])->betweenIncluded($startDate, $endDate);
                    }
                );
            }

            // $allAccounts = AccountFlow::where('account_id', $account->id)->get();
            $tableData[$account->type][$account->id] = $accountAllData;

            if ($account->type == BankAccount::ACCOUNT_TYPE_REVENUE) {
                session(['acccountTransferId' => $account->id]);
                $this->incomeByPeriod = $account->getAdjustedFlowsTotalByDatePeriod($account->id,$startDate, $endDate);
            }else{
                $accccid = session('acccountTransferId') ? session('acccountTransferId') : $account->id;
                $this->incomeByPeriod = $account->getAdjustedFlowsTotalByDatePeriod($accccid,$startDate, $endDate);
            }

            // $tableData[$account->type][$account->id]['total_db'] =
            //     $account->getAdjustedFlowsTotalByDatePeriod($account->id,$startDate, $endDate);

        
            // if (array_key_exists('flows', $tableData[$account->type][$account->id])) {
            //     //reorder flows data
            //     $newFlows = [];
            //     foreach ($tableData[$account->type][$account->id]['flows'] as $flowArray) {
            //             $newFlows[$flowArray['id']] = $flowArray;
            //     }
            //     $tableData[$account->type][$account->id]['flows'] = $newFlows;
            // }
           

            $tableData[$account->type][$account->id] = $this->fillMissingDateValue(
                $period,
                $tableData[$account->type][$account->id],
                $startDate
            );

           
        
        }
      
        //reset period for Forecast
        if ($this->projectionMode == self::PROJECTION_MODE_FORECAST) {
            switch ($rangeValue) {
                case self::RANGE_DAILY:
                    //left as is
                    break;
                case self::RANGE_WEEKLY:
                    $period = CarbonPeriod::since($startDate)->week()->until($endDate);
                    break;
                case self::RANGE_FORTNIGHTLY:
                    $period = CarbonPeriod::since($startDate)->weeks(2)->until($endDate);
                    break;
                case self::RANGE_MONTHLY:
                    $period = CarbonPeriod::since($startDate)->month()->until($endDate);
                    break;
                case self::RANGE_QUARTERLY:
                    $period = CarbonPeriod::since($startDate)->months(3)->until($endDate);
                    break;
            }

       
            $tableData = $this->optimizationTableData($tableData, $period);

        }

        
        $newArray=array();
        $newTableData=array();
        foreach($accounts as $account){
            

           if (array_key_exists("_dates",$tableData[$account->type][$account->id]))
            {
              $newArray['name']=$tableData[$account->type][$account->id]['name'];
              $newArray['dates']=$tableData[$account->type][$account->id]['_dates'];
              $newTableData[]=$newArray;
            }
        }
        
        $periodDates = [];
        foreach ($period as $date) {
            $periodDates[] = $date->format('Y-m-d');
        }

        return response()->json(['data'=>$newTableData]);
       

        // if ($returnType == 'html') {
        //     $response['html'] = view('business.business-allocations-table')
        //         ->with([
        //             'business' => $this->business,
        //             'rangeArray' => $this->getRangeArray(),
        //             'startDate' => $startDate,
        //             'period' => $period,
        //             'periodDates' => $periodDates,
        //             'tableData' => $tableData,
        //             'rangeValue' => $rangeValue,
        //             'accountsSubTypes' => $this->accountsSubTypes,
        //             'projectionMode' => $this->projectionMode,
        //             'tableAttributes' => $this->tableAttributes,
        //             'today' => $today,
        //             'todayShort' => $todayShort
        //         ])->render();

        //     return response()->json($response);
        // } else {
        //     return response()->json([
        //             'error' => $response['error'],
        //             'data' => $this->convertTableDataToFlatArrayForJSONUpdate($tableData)
        //         ]
        //     );
        // }
    }
}

