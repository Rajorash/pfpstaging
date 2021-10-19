<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Business;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use JamesMills\LaravelTimezone\Facades\Timezone;

class RevenueController extends Controller
{
    protected ?Business $business = null;
    protected int $defaultCurrentRangeValue = 14;

    public function table(Request $request)
    {
        $this->business = Business::where('id', $request->business)->with('rollout')->first();

        $maxDate = $this->business->rollout()->max('end_date');
        $minDate = $this->business->rollout()->min('end_date');

        $startDate = $request->startDate ?? Carbon::now()->format('Y-m-d');
        $rangeValue = $request->rangeValue
            ?? session()->get('rangeValue_'.$this->business->id, $this->defaultCurrentRangeValue);
        $endDate = Carbon::parse($startDate)->addDays($rangeValue)->format('Y-m-d');

        $revenueAccounts = $this->business->accounts()
            ->where('type', '=', BankAccount::ACCOUNT_TYPE_REVENUE)
            ->get();

        $tableData = [];
        $RecurringTransactionsController = new RecurringTransactionsController();

        foreach ($revenueAccounts as $revenueAccountRow) {
            $tableData[$revenueAccountRow->id] = [
                'id' => $revenueAccountRow->id,
                'name' => $revenueAccountRow->name,
                'flows' => []
            ];
            foreach ($revenueAccountRow->flows()->get() as $flow) {

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

        return view('business.revenue-entry', [
            'rangeArray' => $this->getRangeArray(),
            'business' => $this->business,
            'startDate' => $startDate,
            'currentRangeValue' => $rangeValue,
            'tableData' => $tableData,
            'period' => CarbonPeriod::create($startDate, $endDate),
            'minDate' => Carbon::parse($minDate)->subMonths(3)->format('Y-m-d'),
            'maxDate' => Carbon::parse($maxDate)->subDays(31)->format('Y-m-d')
        ]);

        /**
         * array:1 [▼
         * 40 => array:3 [▼
         * "id" => 40
         * "name" => "Core"
         * "flows" => array:2 [▼
         * 85 => array:4 [▶]
         * 86 => array:4 [▼
         * "id" => 86
         * "label" => "Estimated Activity"
         * "allocations" => array:2 [▼
         * "2021-10-22" => array:9 [▼
         * "id" => 1238
         * "phase_id" => 25
         * "allocatable_id" => 86
         * "allocatable_type" => "App\Models\AccountFlow"
         * "amount" => 44.0
         * "allocation_date" => "2021-10-22"
         * "manual_entry" => null
         * "created_at" => "2021-10-08T07:29:31.000000Z"
         * "updated_at" => "2021-10-08T07:29:31.000000Z"
         * ]
         * "2021-10-29" => array:9 [▼
         * "id" => 1239
         * "phase_id" => 25
         * "allocatable_id" => 86
         * "allocatable_type" => "App\Models\AccountFlow"
         * "amount" => 44.0
         * "allocation_date" => "2021-10-29"
         * "manual_entry" => null
         * "created_at" => "2021-10-08T07:29:31.000000Z"
         * "updated_at" => "2021-10-08T07:29:31.000000Z"
         * ]
         * ]
         * "recurring" => array:1 [▶]
         * ]
         * ]
         * ]
         * ]
         */


    }

    private function getRangeArray(): array
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
    }

}
