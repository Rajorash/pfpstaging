<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Business;
use App\Traits\GettersTrait;
use App\Traits\TablesTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JamesMills\LaravelTimezone\Timezone;

class RevenueController extends Controller
{
    use GettersTrait, TablesTrait;

    protected ?Business $business = null;
    protected int $defaultCurrentRangeValue = 31;

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function table(Request $request): \Illuminate\View\View
    {
        $this->business = Business::where('id', $request->business)->with('rollout')->first();

        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        $maxDate = $this->business->rollout()->max('end_date');
        $minDate = $this->business->rollout()->min('end_date');

        $startDate = $request->startDate
            ?? session()->get('revenue_startDate'.$this->business->id,
                Carbon::parse($today)->addDays(-4)->format('Y-m-d'));
        $rangeValue = $request->rangeValue
            ?? session()->get('revenue_rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue);
        $endDate = Carbon::parse($startDate)->addDays($rangeValue)->format('Y-m-d');

        return view('business.revenue-entry', [
            'rangeArray' => $this->getRangeArray(),
            'business' => $this->business,
            'startDate' => $startDate,
            'currentRangeValue' => $rangeValue,
            'period' => CarbonPeriod::create($startDate, $endDate),
            'minDate' => Carbon::parse($minDate)->subMonths(3)->format('Y-m-d'),
            'maxDate' => Carbon::parse($maxDate)->subDays(31)->format('Y-m-d'),
            'today' => $today
        ]);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function loadData(Request $request): JsonResponse
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $businessId = $request->businessId ?? null;

        if (!$businessId) {
            $response['error'][] = 'BusinessId not found';
        }

        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        $todayShort = Timezone::convertToLocal(Carbon::now(), 'Y-m-d').' 00:00:00';

        if ($businessId) {
            $this->business = Business::where('id', $businessId)->with('rollout')->first();

            $startDate = $request->startDate
                ?? session()->get('revenue_startDate'.$this->business->id,
                    Carbon::parse($today)->addDays(-4)->format('Y-m-d'));
            $rangeValue = $request->rangeValue
                ?? session()->get('revenue_rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue);
            $endDate = Carbon::parse($startDate)->addDays($rangeValue)->format('Y-m-d');

            $revenueAccounts = $this->business->accounts()
                ->where('type', '=', BankAccount::ACCOUNT_TYPE_REVENUE)
                ->get();

            $tableData = [];

            foreach ($revenueAccounts as $revenueAccountRow) {

                $tableData[$revenueAccountRow->id] = [
                    'id' => $revenueAccountRow->id,
                    'name' => $revenueAccountRow->name,
                    'flows' => []
                ];

                foreach ($revenueAccountRow->flows as $flow) {
                    $allocations = $flow->allocations()
                        ->whereBetween('allocation_date', [$startDate, $endDate])
                        ->get()
                        ->toArray();
                    $allocationsArray = [];

                    foreach ($allocations as $row) {
                        //without Carbon parse toArray() from previous line return 2021-11-2 instead of 2021-11-02
                        $allocationsArray[Carbon::parse($row['allocation_date'])->format('Y-m-d')] = $row;
                    }

                    $tableData[$revenueAccountRow->id]['flows'][$flow->id] = [
                        'id' => $flow->id,
                        'label' => $flow->label,
                        'certainty' => $flow->certainty,
                        'negative' => boolval($flow->negative_flow),
                        'allocations' => $allocationsArray
                    ];
                }
            }

            $response['html'] = view('business.revenue-entry-table')
                ->with([
                    'business' => $this->business,
                    'tableData' => $tableData,
                    'period' => CarbonPeriod::create($startDate, $endDate),
                    'today' => $today,
                    'todayShort' => $todayShort,
                    'seatsCount' => $request->seatsCount
                ])->render();
        }

        return response()->json($response);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function saveData(Request $request): JsonResponse
    {
        $response = [
            'error' => [],
            'html' => [],
        ];

        $businessId = $request->businessId ?? null;

        if (!$businessId) {
            $response['error'][] = 'BusinessId not found';
        }

        if ($businessId) {
            $this->business = Business::where('id', $businessId)->with('rollout')->first();

            $cells = $request->cells ?? null;

            if ($cells) {
                $this->store($cells);
            }
        }

        return response()->json($response);
    }

}
