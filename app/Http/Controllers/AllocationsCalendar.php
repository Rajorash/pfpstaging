<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use function PHPUnit\Framework\containsIdentical;

class AllocationsCalendar extends Controller
{

    protected $currentRangeValue = 14;

    public function calendar(Request $request)
    {
        $business = Business::where('id', $request->business)->first();

        $data = [
            'rangeArray' => $this->getRangeArray(),
            'currentRangeValue' => $this->currentRangeValue,
            'business' => $business
        ];

        return view('v2.allocations-calculator', $data);
    }

    private function getRangeArray()
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
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
        $cells = $request->cells;

        if (!$startDate) {
            $response['error'][] = 'Start date not set';
        }

        if (!$rangeValue) {
            $response['error'][] = 'Range value not set';
        }

//        $startDate = '2021-03-25';//$request->startDate;
//        $rangeValue = 14;//$request->rangeValue;
        $endDate = Carbon::parse($startDate)->addDays($rangeValue - 1)->format('Y-m-d');


        $period = CarbonPeriod::create($startDate, $endDate);
        $tableData = $this->getGridData($rangeValue, $startDate, $endDate, $businessId);


        $response['html'] = view('v2.allocation-table')
            ->with([
                'tableData' => $tableData,
                'period' => $period,
                'startDate' => Carbon::parse($startDate),
                'range' => $rangeValue
            ])->render();

        return response()->json($response);
    }

    private function getGridData($rangeValue, $dateFrom, $dateTo, $businessId, $phaseId = 10)
    {
        // Need accounts to be sorted as below
        $response = [
            BankAccount::ACCOUNT_TYPE_REVENUE => [],
            BankAccount::ACCOUNT_TYPE_PRETOTAL => [],
            BankAccount::ACCOUNT_TYPE_SALESTAX => [],
            BankAccount::ACCOUNT_TYPE_PREREAL => [],
            BankAccount::ACCOUNT_TYPE_POSTREAL => []
        ];

        $result = BankAccount::where('business_id', 2)
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

        $flat = [];
        $names = [];
        foreach ($result as $account_item) {
            $names[$account_item->id] = $account_item->name;
            foreach ($account_item->account_values as $key => $value) {
                $flat[$key] = $value;
            }
        }

        $percents = $this->getPercentValues ($phaseId, $businessId);
        $period = CarbonPeriod::create($dateFrom, $dateTo);
        $complete = $rangeValue + 1;

        foreach ($percents as $type => $acc_id) {
            foreach ($flat as $id => $account_item) {

                if (!array_key_exists($id, $acc_id)) {
                    continue;
                }
                $flows = [];
                foreach ($period as $date) {
                    $income = $this->getIncomeByDate($businessId, $date);

                    switch ($type) {
                        case BankAccount::ACCOUNT_TYPE_REVENUE:
                            $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id]['name'] = $names[$id];
                            $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$date->format('Y-m-d')]
                                = array_key_exists($date->format('Y-m-d 00:00:00'), $account_item)
                                ? $account_item[$date->format('Y-m-d 00:00:00')]
                                : 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$key]['name'] = $value['name'];
                                    $response[BankAccount::ACCOUNT_TYPE_REVENUE][$id][$key][$date->format('Y-m-d')]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                }
                            }
                            break;
                        case BankAccount::ACCOUNT_TYPE_SALESTAX: // Tax amt
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['name'] = $names[$id];
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id][$date->format('Y-m-d')]
                                = array_key_exists($date->format('Y-m-d 00:00:00'), $account_item)
                                ? $account_item[$date->format('Y-m-d 00:00:00')]
                                : 0;
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['transfer'][$date->format('Y-m-d')] = ($income > 0)
                                ? round($income - $income / ($percents[$type][$id] / 100 + 1), 4)
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date->format('Y-m-d')]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date->format('Y-m-d')]
                                        : $flow_total + $flows[$id][$key][$date->format('Y-m-d')];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id]['total'][$date->format('Y-m-d')] = $flow_total;
                            if(array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_SALESTAX][$id] += $flows[$id];
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_PRETOTAL:
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['name'] = $names[$id];
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id][$date->format('Y-m-d')]
                                = array_key_exists($date->format('Y-m-d 00:00:00'),
                                $account_item)
                                ? $account_item[$date->format('Y-m-d 00:00:00')]
                                : 0;
                            $salestax = data_get($percents, 'salestax');
                            $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
                            $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['transfer'][$date->format('Y-m-d')]
                                = (is_numeric($percents[$type][$id]))
                                ? round($nsp * ($percents[$type][$id] / 100), 4)
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date->format('Y-m-d')]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date->format('Y-m-d')]
                                        : $flow_total + $flows[$id][$key][$date->format('Y-m-d')];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id]['total'][$date->format('Y-m-d')] = $flow_total;
                            if(array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_PRETOTAL][$id] += $flows[$id];
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_PREREAL:
                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['name'] = $names[$id];
                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id][$date->format('Y-m-d')]
                                = array_key_exists($date->format('Y-m-d 00:00:00'),
                                $account_item)
                                ? $account_item[$date->format('Y-m-d 00:00:00')]
                                : 0;
                            $prereal = $this->getPrePrereal($income, $percents);

                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['transfer'][$date->format('Y-m-d')]
                                = (is_numeric($percents[$type][$id]))
                                ? round($prereal * ($percents[$type][$id] / 100), 4)
                                : 0;
                            $flow_total = 0;
                            foreach ($account_item as $key => $value) {
                                if (is_integer($key)) {
                                    $flows[$id][$key]['name'] = $value['name'];
                                    $flows[$id][$key][$date->format('Y-m-d')]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date->format('Y-m-d')]
                                        : $flow_total + $flows[$id][$key][$date->format('Y-m-d')];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id]['total'][$date->format('Y-m-d')] = $flow_total;
                            if(array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_PREREAL][$id] += $flows[$id];
                            }
                            break;

                        case BankAccount::ACCOUNT_TYPE_POSTREAL:
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['name'] = $names[$id];
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id][$date->format('Y-m-d')]
                                = array_key_exists($date->format('Y-m-d 00:00:00'),
                                $account_item)
                                ? $account_item[$date->format('Y-m-d 00:00:00')]
                                : 0;
                            $prereal = $this->getPrePrereal($income, $percents);
                            $prereal_percents = array_sum($percents[BankAccount::ACCOUNT_TYPE_PREREAL]);

                            // Real Revenue = $prereal - $prereal * ($prereal_percents / 100)
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['transfer'][$date->format('Y-m-d')]
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
                                    $flows[$id][$key][$date->format('Y-m-d')]
                                        = array_key_exists($date->format('Y-m-d 00:00:00'), $value)
                                        ? $value[$date->format('Y-m-d 00:00:00')]
                                        : 0;
                                    $flow_total = $value['negative']
                                        ? $flow_total - $flows[$id][$key][$date->format('Y-m-d')]
                                        : $flow_total + $flows[$id][$key][$date->format('Y-m-d')];
                                }
                            }
                            $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id]['total'][$date->format('Y-m-d')] = $flow_total;
                            if(array_key_exists($key, $flows[$id]) && count($flows[$id][$key]) == $complete) {
                                $response[BankAccount::ACCOUNT_TYPE_POSTREAL][$id] += $flows[$id];
                            }
                            break;
                    }
                }
            }
        }

        return $response;
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
        $pretotal = count($pretotal) > 0 ? $pretotal[key($pretotal)] : null;
        $pretotal_amt = (is_numeric($pretotal))
            ? round($nsp * ($pretotal / 100), 4)
            : 0;

        return $nsp - $pretotal_amt;
    }

    private function getPercentValues ($phaseId, $businessId)
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
