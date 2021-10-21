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

class RevenueController extends Controller
{
    use GettersTrait, TablesTrait;

    protected ?Business $business = null;
    protected int $defaultCurrentRangeValue = 14;

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function table(Request $request): \Illuminate\View\View
    {
        $this->business = Business::where('id', $request->business)->with('rollout')->first();

        $maxDate = $this->business->rollout()->max('end_date');
        $minDate = $this->business->rollout()->min('end_date');

        $startDate = $request->startDate
            ?? session()->get('revenue_startDate'.$this->business->id, Carbon::now()->format('Y-m-d'));
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
            'maxDate' => Carbon::parse($maxDate)->subDays(31)->format('Y-m-d')
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

        if ($businessId) {
            $this->business = Business::where('id', $businessId)->with('rollout')->first();

            $startDate = $request->startDate
                ?? session()->get('revenue_startDate'.$this->business->id, Carbon::now()->format('Y-m-d'));
            $rangeValue = $request->rangeValue
                ?? session()->get('revenue_rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue);
            $endDate = Carbon::parse($startDate)->addDays($rangeValue)->format('Y-m-d');

            $revenueAccounts = $this->business->accounts()
                ->where('type', '=', BankAccount::ACCOUNT_TYPE_REVENUE)
                ->get();

            $tableData = [];
            $RecurringTransactionsController = new RecurringTransactionsController();


            $RecurringPipelineController = new RecurringPipelineController();
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
                        $allocationsArray[$row['allocation_date']] = $row;
                    }

                    $tableData[$revenueAccountRow->id]['flows'][$flow->id] = [
                        'id' => $flow->id,
                        'label' => $flow->label,
                        'allocations' => $allocationsArray,
                        'recurring' => $flow->recurringTransactions()->count() ?
                            $RecurringTransactionsController
                                ->getAllFlowsForecasts($flow->recurringTransactions, $startDate, $endDate)
                            : null
                    ];
                }
            }

            if (count($this->business->pipelines)) {
                $tableData['pipelines'] = [];
                foreach ($this->business->pipelines as $pipeline) {
                    $tableData['pipelines'][$pipeline->id] = $pipeline;
                    $tableData['pipelines'][$pipeline->id]['forecast'] = $RecurringPipelineController->getForecast($pipeline, $startDate, $endDate);
                }
            }

            $response['html'] = view('business.revenue-entry-table')
                ->with([
                    'business' => $this->business,
                    'tableData' => $tableData,
                    'period' => CarbonPeriod::create($startDate, $endDate),
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
