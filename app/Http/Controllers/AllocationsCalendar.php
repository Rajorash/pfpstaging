<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\containsIdentical;

class AllocationsCalendar extends Controller
{
    use GettersTrait;

    protected $defaultCurrentRangeValue = 14;
    protected $business;

    public function calendar(Request $request)
    {
        $this->business = Business::where('id', $request->business)->first();

        $maxDate = $this->business->rollout()->max('end_date');
        $minDate = $this->business->rollout()->min('end_date');


        $data = [
            'rangeArray' => $this->getRangeArray(),
            'business' => $this->business,
            'startDate' => session()->get('startDate_'.$this->business->id, Carbon::now()->format('Y-m-d')),
            'currentRangeValue' => session()->get('rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue),
            'minDate' => Carbon::parse($minDate)->format('Y-m-d'),
            'maxDate' => Carbon::parse($maxDate)->format('Y-m-d'),
        ];

        return view('business.allocations-calculator', $data);
    }

    private function getRangeArray()
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
    }

    public function store($cells)
    {
        if (is_array($cells) && count($cells) > 0) {
            foreach ($cells as $singleCell) {
                preg_match('/\w+_(\d+)_(\d{4}-\d{2}-\d{2})/', $singleCell['cellId'], $matches);
                $allocation_id = (integer) $matches[1];
                $date = $matches[2];
                $value = (float) $singleCell['cellValue'];

                $this->storeSingle('flow', $allocation_id, $value, $date);
            }
        }

        return null;
    }

    /**
     * Validate and store the Allocation
     *
     * @return void
     */
    public function storeSingle($type, $allocation_id, $amount, $date)
    {
        /*        $this->validate([
                    'amount' => 'numeric|nullable'
                ]);

                $data = array(
                    'amount' => $amount
                );
        */
        $phaseId = $this->business->getPhaseIdByDate($date);
        $values = [
            $amount,
            $date,
            $phaseId
        ];

        if ($type == 'flow') {
            $account = $this->getFlowAccount($allocation_id);
        } else {
            $account = $this->getBackAccount($allocation_id);
        }
        $account->allocate(...$values);
    }

    public function updateData(Request $request)
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $startDate = $request->startDate;
        $rangeValue = $request->rangeValue;
        $businessId = $request->businessId;
        $business = Business::find($businessId);
        $phase = $business->getPhaseIdByDate($startDate);

        $cells = $request->cells;
        $this->business = Business::where('id', $businessId)->first();

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

        $tableData = $this->getGridData($rangeValue, $startDate, $endDate, $businessId);

        $response['html'] = view('business.allocation-table')
            ->with([
                'tableData' => $tableData,
                'phase' => $phase,
                'period' => $period,
                'startDate' => Carbon::parse($startDate),
                'range' => $rangeValue,
                'business' => $business
            ])->render();

        return response()->json($response);
    }

    private function getGridData($rangeValue, $dateFrom, $dateTo, $businessId)
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
                                Cache::forget($key);
                            }
                            break;
                        case BankAccount::ACCOUNT_TYPE_SALESTAX: // Tax amt
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['transfer'][$date_ymd] = ($income > 0)
                                ? round($income - $income / ($percents[$type][$id] / 100 + 1), 4)
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
                                }
                            }

                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['total'][$date_ymd] = $flow_total;

                            if (array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['transfer'][$date_ymd];

                            $previousDate = Carbon::parse($date)->subDays(1)->format('Y-m-d');

                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id])) {
                                $actualValue += $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }

                            if ($response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date_ymd] != $actualValue) {
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
                                $actualValue += $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }
                            if ($response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date_ymd] != $actualValue) {
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
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['total'][$date_ymd] = $flow_total;
                            if (array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['transfer'][$date_ymd];
                            $previousDate = Carbon::parse($date->format('Y-m-d'))->subDays(1)->format('Y-m-d');
                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id])) {
                                $actualValue += $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }
                            if ($response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd] != $actualValue) {
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date_ymd] = $actualValue;
                                $this->storeSingle('account', $id, $actualValue, $date->format('Y-m-d'));
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_POSTREAL:
                            $prereal = $this->getPrePrereal($income, $percents);
                            $prereal_percents = array_sum($percents[BankAccount::ACCOUNT_TYPE_PREREAL]);

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
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['total'][$date_ymd] = $flow_total;
                            if (array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id] += $flows[$id];
                            }

                            $actualValue = $flow_total +
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['transfer'][$date_ymd];
                            $previousDate = Carbon::parse($date->format('Y-m-d'))->subDays(1)->format('Y-m-d');
                            if (array_key_exists($previousDate, $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id])) {
                                $actualValue += $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$previousDate];
                            } else {
                                $previousNonZero = $this->getPreviousNonZeroValue($id, $dateFrom);
                                if (is_numeric($previousNonZero)) {
                                    $actualValue += $previousNonZero;
                                }
                            }
                            if ($response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date_ymd] != $actualValue) {
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

    private function getRawData($businessId, $dateFrom, $dateTo)
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

                $item->account_values = [
                    $item->id => $item->allocations->pluck('amount', 'allocation_date')->toArray()
                        + $flows
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
            Cache::put($key, $getIncomeByDate);
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

        $phasePercentValues = Cache::get($key);

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

            Cache::put($key, $phasePercentValues);
        }

        return $phasePercentValues;
    }
}
