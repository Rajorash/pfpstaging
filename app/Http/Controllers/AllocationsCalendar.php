<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Config;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use JamesMills\LaravelTimezone\Facades\Timezone;

//use function PHPUnit\Framework\containsIdentical;

class AllocationsCalendar extends Controller
{
    use GettersTrait;

    protected int $defaultCurrentRangeValue = 14;
    protected ?Business $business = null;

    /**
     * @throws AuthorizationException
     */
    public function calendar(Request $request)
    {
        $this->business = Business::where('id', $request->business)->with('rollout')->first();

        $maxDate = $this->business->rollout()->max('end_date');
        $minDate = $this->business->rollout()->min('end_date');
        $startDate = session()->get('startDate_'.$this->business->id, Timezone::convertToLocal(Carbon::now(), 'Y-m-d'));

        $this->pushRecurringTransactionData(
            $this->business->id,
            $startDate,
            Carbon::now()->addMonths(13)->format('Y-m-d'),
            true
        );

        $data = [
            'rangeArray' => $this->getRangeArray(),
            'business' => $this->business,
            'startDate' => $startDate,
            'currentRangeValue' => session()->get('rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue),
            'minDate' => Carbon::parse($minDate)->subMonths(3)->format('Y-m-d'),
            'maxDate' => Carbon::parse($maxDate)->subDays(31)->format('Y-m-d'),
        ];

        return view('business.allocations-calculator', $data);
    }

    private function getRangeArray(): array
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
    }

    public function store($cells, bool $checkIsValuePresent = false)
    {
        if (is_array($cells) && count($cells) > 0) {
            foreach ($cells as $singleCell) {
                preg_match('/(\w+)_(\d+)_(\d{4}-\d{2}-\d{2})/', $singleCell['cellId'], $matches);
                $allocation_id = (integer) $matches[2];
                $date = $matches[3];
                $value = (float) $singleCell['cellValue'];

                $this->storeSingle(
                    $matches[1],
                    $allocation_id,
                    $value,
                    $date,
                    ($matches[1] == 'account'),
                    $checkIsValuePresent
                );
            }
        }

        return null;
    }

    /**
     * Validate and store the Allocation
     * @param  string  $type
     * @param  int  $allocation_id
     * @param $amount
     * @param  string  $date
     * @param  bool  $manual_entry
     * @param  bool  $checkIsValuePresent
     */
    public function storeSingle(
        string $type,
        int $allocation_id,
        $amount,
        string $date,
        bool $manual_entry = false,
        bool $checkIsValuePresent = false
    ) {
        $phaseId = $this->business->getPhaseIdByDate($date);

        if ($type == 'flow') {
            $account = $this->getFlowAccount($allocation_id);
        } else {
            $account = $this->getBankAccount($allocation_id);
        }

        $account->allocate($amount, $date, $phaseId, $manual_entry, $checkIsValuePresent);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateData(Request $request): JsonResponse
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $startDate = $request->startDate ?? null;
        $rangeValue = $request->rangeValue ?? null;
        $businessId = $request->businessId ?? null;
        $business = Business::find($businessId);
        $phase = $business->getPhaseIdByDate($startDate);

        $cells = $request->cells;
        $this->business = Business::where('id', $businessId)
            ->with([
                'owner',
                'license',
                'collaboration',
                'license.advisor',
                'accounts',
                'accounts.flows',
                'rollout'
            ])
            ->first();

        if (!$startDate) {
            $response['error'][] = 'Start date not set';
        } else {
            session(['startDate_'.$businessId => $startDate]);
        }

        if (!$rangeValue) {
            $response['error'][] = 'Range value not set';
        } else {
            session(['rangeValue_'.$businessId => $rangeValue]);
        }

        $endDate = Carbon::parse($startDate)->addDays($rangeValue - 1)->format('Y-m-d');
        $period = CarbonPeriod::create($startDate, $endDate);

        $this->store($cells);

        $RecurringTransactionsController = new RecurringTransactionsController();
        $recurring = [];
        $rawData = $this->getRawData($businessId, $startDate, $endDate);
        foreach ($rawData as $account_item) {
            foreach ($account_item->flows as $key => $value) {
                $recurring[$value->id] = null;
                if ($value->recurringTransactions->count()) {
                    $recurring[$value->id] = $RecurringTransactionsController
                        ->getAllFlowsForecasts($value->recurringTransactions, $startDate, $endDate);
                }
            }
        }

        $tableData = $this->getGridData($rangeValue, $startDate, $endDate, $businessId);

        $response['html'] = view('business.allocation-table')
            ->with([
                'tableData' => $tableData,
                'phase' => $phase,
                'period' => $period,
                'startDate' => Carbon::parse($startDate),
                'range' => $rangeValue,
                'business' => $business,
                'recurring' => $recurring
            ])->render();

        return response()->json($response);
    }

    /**
     * @throws AuthorizationException
     */
    public function getGridData($rangeValue, $dateFrom, $dateTo, $businessId): array
    {
        $this->business = Business::where('id', $businessId)->first();

        $this->authorize('view', $this->business);

        // Need accounts to be sorted as below
        $response = [
            BankAccount::ACCOUNT_TYPE_REVENUE => [],
            BankAccount::ACCOUNT_TYPE_PRETOTAL => [],
            BankAccount::ACCOUNT_TYPE_SALESTAX => [],
            BankAccount::ACCOUNT_TYPE_PREREAL => [],
            BankAccount::ACCOUNT_TYPE_POSTREAL => []
        ];

        $result = $this->getRawData($businessId, $dateFrom, $dateTo);

        $flat = [];
        $names = [];
        foreach ($result as $account_item) {
            $names[$account_item->id] = $account_item->name;
            foreach ($account_item->account_values as $key => $value) {
                $flat[$key] = $value;
            }
        }

//        dd($response);

        $period = CarbonPeriod::create($dateFrom, $dateTo);
        $complete = $rangeValue + 1;

        $phaseId = $this->business->getPhaseIdByDate($dateFrom);
        $types = $this->getPercentValues($phaseId, $businessId);

        foreach ($types as $type => $acc_id) {
            foreach ($flat as $id => $account_item) {

                if (!array_key_exists($id, $acc_id)) {
                    continue;
                }

                $flows = [];

                foreach ($period as $date) {

                    $date_ymd = $date->format('Y-m-d');

                    $income = $this->getIncomeByDate($businessId, $date_ymd);
                    $phaseId = $this->business->getPhaseIdByDate($date);
                    $percents = $this->getPercentValues($phaseId, $businessId);

                    $response[$type][$id]['name'] = $names[$id];
                    $response[$type][$id][$date_ymd]
                        = array_key_exists($date->format('Y-m-d 00:00:00'), $account_item)
                        ? $account_item[$date->format('Y-m-d 00:00:00')]
                        : 0;

                    switch ($type) {
                        case BankAccount::ACCOUNT_TYPE_REVENUE:
                            $totalRevenue = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$key]['name'] = $value['name'];
                                    $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$key][$date_ymd]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $totalRevenue += $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$key][$date_ymd];
                                }
                            }
                            if ($response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$date_ymd] != $totalRevenue) {
                                $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$date_ymd] = $totalRevenue;
                                $this->storeSingle('account', $id, $totalRevenue, $date_ymd);
                                $key = 'getIncomeByDate_'.$businessId.'_'.$date_ymd;
                                if (Config::get('app.pfp_cache')) {
                                    Cache::forget($key);
                                }
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_SALESTAX: // Tax amt
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['transfer'][$date_ymd] =
                                $this->calculateSalestaxTransfer($id, $income, $percents);

                            $flow_total = 0;

                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date_ymd]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date_ymd]
                                        : $flow_total + $flows[$id][$key][$date_ymd];
                                } elseif ($key == $date->format('Y-m-d 00:00:00')) {
                                    $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['manual'][$date_ymd] = $value[1];
                                }
                            }

                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['total'][$date_ymd] = $flow_total;

                            if (
                                isset($flows[$id])
                                && array_key_exists($key, $flows[$id])
                                && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['transfer'][$date_ymd];

                            $previousDate = Carbon::parse($date)->subDays(1)->format('Y-m-d');

                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id])) {
                                $actualValue += is_array($response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$previousDate])
                                    ? $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$previousDate][0]
                                    : $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }

                            $stored_value = is_array($response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date_ymd])
                                ? $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date_ymd][0]
                                : $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date_ymd];

                            if ($stored_value != $actualValue &&
                                !$this->hasManualEntry($response, BankAccount::ACCOUNT_TYPE_SALESTAX, $id, $date_ymd)
                            ) {
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date_ymd] = $actualValue;
                                $this->storeSingle('account', $id, $actualValue, $date_ymd);
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_PRETOTAL:
                            $salestax = data_get($percents, 'salestax');
                            $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
                            $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['transfer'][$date_ymd]
                                = (is_numeric($percents[$type][$id]))
                                ? round($nsp * ($percents[$type][$id] / 100), 4)
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date_ymd]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date_ymd]
                                        : $flow_total + $flows[$id][$key][$date_ymd];
                                } elseif ($key == $date->format('Y-m-d 00:00:00')) {
                                    $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['manual'][$date_ymd] = $value[1];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['total'][$date_ymd] = $flow_total;
                            if (array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['transfer'][$date_ymd];
                            $previousDate = Carbon::parse($date)->subDays(1)->format('Y-m-d');
                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id])) {
                                $actualValue += is_array($response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$previousDate])
                                    ? $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$previousDate][0]
                                    : $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }

                            $stored_value = is_array($response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date_ymd])
                                ? $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date_ymd][0]
                                : $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date_ymd];

                            if ($stored_value != $actualValue
                                && (!isset($response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['manual'][$date_ymd])
                                    || !$response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['manual'][$date_ymd])
                            ) {
                                $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date_ymd] = $actualValue;
                                $this->storeSingle('account', $id, $actualValue, $date_ymd);
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_PREREAL:
                            $prereal = $this->getPrePrereal($income, $percents);

                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['transfer'][$date_ymd]
                                = (is_numeric($percents[$type][$id]))
                                ? round($prereal * ($percents[$type][$id] / 100), 4)
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date_ymd]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date_ymd]
                                        : $flow_total + $flows[$id][$key][$date_ymd];
                                } elseif ($key == $date->format('Y-m-d 00:00:00')) {
                                    $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['manual'][$date_ymd] = $value[1];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['total'][$date_ymd] = $flow_total;

                            if ( array_key_exists($id, $flows)
                                && array_key_exists($key, $flows[$id])
                                && count($flows[$id][$key]) == $complete)
                            {
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['transfer'][$date_ymd];
                            $previousDate = Carbon::parse($date->format('Y-m-d'))->subDays(1)->format('Y-m-d');
                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id])) {
                                $actualValue += is_array($response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$previousDate])
                                    ? $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$previousDate][0]
                                    : $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }

                            $stored_value = is_array($response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd])
                                ? $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd][0]
                                : $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd];

                            if ($stored_value != $actualValue &&
                                !$this->hasManualEntry($response, BankAccount::ACCOUNT_TYPE_PREREAL, $id, $date_ymd)
                            ) {
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd] = $actualValue;
                                $this->storeSingle('account', $id, $actualValue, $date->format('Y-m-d'));
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_POSTREAL:
                            $prereal = $this->getPrePrereal($income, $percents);

                            $prereal_percents = key_exists(BankAccount::ACCOUNT_TYPE_PREREAL, $percents)
                                ? array_sum($percents[BankAccount::ACCOUNT_TYPE_PREREAL])
                                : 0;

                            // Real Revenue = $prereal - $prereal * ($prereal_percents / 100)
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['transfer'][$date_ymd]
                                = (is_numeric($percents[$type][$id]))
                                ? round(
                                    ($prereal - $prereal * ($prereal_percents / 100)) * ($percents[$type][$id] / 100),
                                    4
                                )
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date_ymd]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date_ymd]
                                        : $flow_total + $flows[$id][$key][$date_ymd];
                                } elseif ($key == $date->format('Y-m-d 00:00:00')) {
                                    $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['manual'][$date_ymd] = $value[1];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['total'][$date_ymd] = $flow_total;
                            if ( array_key_exists($id, $flows)
                                && array_key_exists($key, $flows[$id])
                                && count($flows[$id][$key]) == $complete)
                            {
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['transfer'][$date_ymd];
                            $previousDate = Carbon::parse($date->format('Y-m-d'))->subDays(1)->format('Y-m-d');
                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id])) {
                                $actualValue += is_array($response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$previousDate])
                                    ? $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$previousDate][0]
                                    : $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }

                            $stored_value = is_array($response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date_ymd])
                                ? $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date_ymd][0]
                                : $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date_ymd];

                            if ($stored_value != $actualValue &&
                                !$this->hasManualEntry($response, BankAccount::ACCOUNT_TYPE_POSTREAL, $id, $date_ymd)
                            ) {
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date_ymd] = $actualValue;
                                $this->storeSingle('account', $id, $actualValue, $date->format('Y-m-d'));
                            }
                            break;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Calculate the amount to transfer for a salestax account based
     * on the income and percentage value
     *
     * @param [type] $id
     * @param [type] $income
     * @param [type] $percents
     * @return int|float
     */
    private function calculateSalestaxTransfer($id, $income, $percents)
    {
        $transfer_amount = 0;

        if ($income > 0) {
            $transfer_amount = round($income - $income / ($percents[BankAccount::ACCOUNT_TYPE_SALESTAX][$id] / 100 + 1),
                4);
        }

        return $transfer_amount;
    }

    /**
     * Returns true if a manual entry exists for the given account
     * type, id and date
     *
     * @param $response
     * @param $type
     * @param $id
     * @param $date_ymd
     * @return boolean
     */
    private function hasManualEntry($response, $type, $id, $date_ymd): bool
    {
        if (isset($response[$type][$id]['manual'][$date_ymd])) {
            return true;
        }

        return false;
    }

    private function getRawData($businessId, $dateFrom, $dateTo): array
    {
        return BankAccount::where('business_id', $businessId)
            ->with('flows.allocations', function ($query) use ($dateFrom, $dateTo) {
                return $query->where('allocation_date', '>=', $dateFrom)
                    ->where('allocation_date', '<=', $dateTo);
            })
            ->with('allocations', function ($query) use ($dateFrom, $dateTo) {
                return $query->where('allocation_date', '>=', $dateFrom)
                    ->where('allocation_date', '<=', $dateTo);
            })
            ->get()
            ->map(function ($item) {
                $flows = [];
                foreach ($item->flows as $flow) {
                    $flows += [
                        $flow->id => $flow->allocations->pluck('amount', 'allocation_date')->toArray()
                            + ['negative' => (bool) $flow->negative_flow]
                            + ['name' => $flow->label]
                    ];
                }

                $amounts = $item->allocations->pluck('amount', 'allocation_date')->toArray();
                $manuals = $item->allocations->pluck('manual_entry', 'allocation_date')->toArray();

                $item->account_values = [
                    $item->id => array_merge_recursive($amounts, $manuals) + $flows
                ];
                return $item;
            })->all();
    }

    private function getIncomeByDate($businessId, $date)
    {
        $key = 'getIncomeByDate_'.$businessId.'_'.$date;
        $getIncomeByDate = Cache::get($key);

        if ($getIncomeByDate === null) {
            $getIncomeByDate = BankAccount::where('type', 'revenue')->where('business_id', $businessId)
                ->with('allocations', function ($query) use ($date) {
                    return $query->where('allocation_date', $date);
                })
                ->get()
                ->map(function ($item) {
                    return collect($item->toArray())
                        ->only('allocations')
                        ->all();
                })
                ->map(function ($a_item) {
                    return count($a_item['allocations']) > 0
                        ? $a_item['allocations'][0]['amount']
                        : 0;
                })->sum();
            if (Config::get('app.pfp_cache')) {
                Cache::put($key, $getIncomeByDate, now()->addMinutes(10));
            }
        }

        return $getIncomeByDate;
    }

    private function getPreviousNonZeroValue($accountId, $dateFrom)
    {
        $result = BankAccount::where('id', $accountId)
            ->with('allocations', function ($query) use ($dateFrom) {
                return $query->where('allocation_date', '<', $dateFrom)
                    ->where('amount', '>', 0)
                    ->orderBy('allocation_date', 'desc');
            })
            ->get()//;
            ->map(function ($item) {
                return $item->allocations->slice(0, 1)->pluck(['amount']);
            })->pop()->toArray();

        return (count($result) > 0) ? $result[0] : null;
    }

    /**
     * Get the value of revenue after excluding sales tax and saving to drip account
     *
     * @param $income
     * @param $percents
     * @return float|int
     */
    private function getPrePrereal($income, $percents)
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

    private function getPercentValues($phaseId, $businessId)
    {
        $key = 'phasePercentValues_'.$phaseId.'_'.$businessId;

        $phasePercentValues = Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($phasePercentValues === null) {

            $phasePercentValues = BankAccount::where('business_id', $businessId)
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

            if (Config::get('app.pfp_cache')) {
                Cache::put($key, $phasePercentValues);
            }
        }

        return $phasePercentValues;
    }

    /** Put data from RecurringTasks into allocation table
     * @param  int  $businessId
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  bool  $ifThisAllocationsPage
     * @throws AuthorizationException
     */
    public function pushRecurringTransactionData(
        int $businessId,
        string $startDate,
        string $endDate,
        bool $ifThisAllocationsPage
    ) {
        $RecurringTransactionsController = new RecurringTransactionsController();
        $recurring = [];
        $rawData = $this->getRawData($businessId, $startDate, $endDate);
        foreach ($rawData as $account_item) {
            foreach ($account_item->flows as $key => $value) {
                $recurring[$value->id] = null;
                if ($value->recurringTransactions->count()) {
                    $recurring[$value->id] = $RecurringTransactionsController
                        ->getAllFlowsForecasts($value->recurringTransactions, $startDate, $endDate);
                }
            }
        }

        if (!empty($recurring)) {

            $rtEntryData = [];

            foreach ($recurring as $flowId => $flowData) {
                if ($flowData) {
                    $keySuffix = 'flow_'.$flowId.'_';
                    foreach ($flowData as $flowDataTitle => $flowDataRecords) {
                        if (isset($flowDataRecords['forecast'])) {
                            foreach ($flowDataRecords['forecast'] as $date => $value) {
                                if (!isset($rtEntryData[$keySuffix.$date])) {
                                    $rtEntryData[$keySuffix.$date] = 0;
                                }
                                $rtEntryData[$keySuffix.$date] += $value;
                            }
                        }
                    }
                }
            }

            if (!empty($rtEntryData)) {
                $push2Table = [
                    'businessId' => $businessId,
                    'startDate' => $startDate,
                    'rangeValue' => 31,
                    'cells' => []
                ];
                foreach ($rtEntryData as $cellId => $cellValue) {
                    $push2Table['cells'][] = ['cellId' => $cellId, 'cellValue' => $cellValue];
                }

                //if we call this method from ProjectionController::class
                if (!$this->business) {
                    $this->business = Business::findOrFail($businessId);
                }

                //all period - long process
                //$rangeValue = count(CarbonPeriod::create($startDate, $endDate)->toArray());
                $rangeValue = 31;

                $this->store($push2Table['cells'], true);

                if (!$ifThisAllocationsPage) {
                    //Allocations page has this functionality, and haven't a reason to call it again
                    $this->getGridData($rangeValue, $startDate, $endDate, $businessId);
                }
            }
        }
    }
}
