<?php

namespace App\Http\Controllers;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\RecurringTransactions;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use mysql_xdevapi\Result;

class RecurringTransactionsController extends Controller
{
    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function list(int $bankAccountId, int $accountFlowId)
    {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $accountFlow = AccountFlow::findOrfail($accountFlowId);

        $recurringTransactions = RecurringTransactions::where('account_id', $accountFlow->id)
            ->orderBy('title')
            ->get();

        return view(
            'recurring.list',
            [
                'bankAccount' => $bankAccount,
                'accountFlow' => $accountFlow,
                'recurringTransactions' => $recurringTransactions
            ]
        );
    }

    /** Get compiled forecast for Flow
     * @param  Collection  $recurringTransactionsArray
     * @param  string|null  $dateStart
     * @param  string|null  $dateEnd
     * @return array
     */
    public function getAllFlowsForecasts(
        Collection $recurringTransactionsArray,
        string $dateStart = null,
        string $dateEnd = null
    ): array {
        $result = [];
        foreach ($recurringTransactionsArray as $recurringTransactions) {
            $result[$recurringTransactions->title] =
                [
                    'forecast' => $this->getForecast($recurringTransactions, $dateStart, $dateEnd),
                    'title' => $recurringTransactions->title,
                    'description' => $recurringTransactions->description,
                ];
        }

        ksort($result);

        return $result;
    }

    /**
     * @param  RecurringTransactions  $recurringTransactions
     * @param  string|null  $periodDateStart
     * @param  string|null  $periodDateEnd
     * @return array
     */
    public function getForecast(
        RecurringTransactions $recurringTransactions,
        string $periodDateStart = null,
        string $periodDateEnd = null
    ): array {
        $datesRangeFromUserLimit = [];
        $intersection = [];
        $days = [];

        //get dates from recurringTransactions
        $startDate = Carbon::parse($recurringTransactions->date_start);
        $endDate = $recurringTransactions->date_end ? Carbon::parse($recurringTransactions->date_end) : null;

        //if end date not set - use periodDateEnd or calculate from now
        if (!$endDate) {
            $endDate = $periodDateEnd ? Carbon::parse($periodDateEnd)
                : ($recurringTransactions->repeat_every_type == RecurringTransactions::REPEAT_YEAR
                    ? Carbon::now()->addYears(3)
                    : Carbon::now()->addMonths(3)
                );
        }

        $datesRangeFromRecurringTransaction = $this->getTimestampsFromPeriod($startDate, $endDate);

        if ($periodDateStart && $periodDateEnd) {
            $datesRangeFromUserLimit = $this->getTimestampsFromPeriod($periodDateStart, $periodDateEnd);
            $intersection = array_intersect($datesRangeFromRecurringTransaction, $datesRangeFromUserLimit);
        } else {
            $intersection = $datesRangeFromRecurringTransaction;
        }

        if (!empty($intersection)) {
            sort($intersection);

            //intersected date
            $allowedPeriodStart = Carbon::createFromTimestamp(Arr::first($intersection));
            $allowedPeriodEnd = Carbon::createFromTimestamp(Arr::last($intersection));

            if (isset($recurringTransactions->repeat_rules['days'])) {
                foreach ($recurringTransactions->repeat_rules['days'] as $dayName) {
                    $this->_getDaysByName($days,
                        $dayName,
                        $recurringTransactions->repeat_every_number,
                        $recurringTransactions->repeat_every_type,
                        $allowedPeriodStart,
                        $allowedPeriodEnd,
                        $recurringTransactions->value
                    );
                }
            } else {
                $this->_getDaysByName($days,
                    null,
                    $recurringTransactions->repeat_every_number,
                    $recurringTransactions->repeat_every_type,
                    $allowedPeriodStart,
                    $allowedPeriodEnd,
                    $recurringTransactions->value
                );
            }

            ksort($days);
        }

        return $days;
    }

    /**return all timestamps from period
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    private function getTimestampsFromPeriod(
        string $start,
        string $end
    ): array {
        $dates = [];
        if (Carbon::parse($start)->lte(Carbon::parse($end))) {
            $period = CarbonPeriod::create($start, $end);
            foreach ($period->toArray() as $carbonDate) {
                $dates[] = $carbonDate->timestamp;
            }
        }

        return $dates;
    }

    /** retrun days by text name
     * @param  array  $days
     * @param  ?string  $dayName
     * @param  int  $intervalValue
     * @param  string  $intervalType
     * @param  Carbon  $startDate
     * @param  Carbon  $endDate
     * @param  float  $value
     * @return void
     */
    private function _getDaysByName(
        array &$days,
        ?string $dayName,
        int $intervalValue,
        string $intervalType,
        Carbon $startDate,
        Carbon $endDate,
        float $value
    ) {
        if ($value) {
            $startDate = Carbon::parse($startDate);

            if ($intervalType == RecurringTransactions::REPEAT_DAY) {
                for ($date = $startDate; $date->lte($endDate); $date->addDays($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == RecurringTransactions::REPEAT_WEEK) {
                $startDate = $startDate->modify('this '.$dayName);
                for ($date = $startDate; $date->lte($endDate); $date->addWeeks($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == RecurringTransactions::REPEAT_MONTH) {
                for ($date = $startDate; $date->lte($endDate); $date->addMonths($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }

            if ($intervalType == RecurringTransactions::REPEAT_YEAR) {
                for ($date = $startDate; $date->lte($endDate); $date->addYears($intervalValue)) {
                    $days[$date->format('Y-m-d')] = $value;
                }
            }
        }
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(
        int $bankAccountId,
        int $accountFlowId
    ) {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $accountFlow = AccountFlow::findOrfail($accountFlowId);
        $recurringTransactions = null;

        return view(
            'recurring.create',
            [
                'bankAccount' => $bankAccount,
                'accountFlow' => $accountFlow,
                'recurringTransactions' => $recurringTransactions
            ]
        );
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @param  int  $recurringId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(
        int $bankAccountId,
        int $accountFlowId,
        int $recurringId
    ) {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $accountFlow = AccountFlow::findOrfail($accountFlowId);
        $recurringTransactions = RecurringTransactions::findOrfail($recurringId);

        return view(
            'recurring.create',
            [
                'bankAccount' => $bankAccount,
                'accountFlow' => $accountFlow,
                'recurringTransactions' => $recurringTransactions
            ]
        );
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @param  int  $recurringId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(
        int $bankAccountId,
        int $accountFlowId,
        int $recurringId
    ) {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $accountFlow = AccountFlow::findOrfail($accountFlowId);
        $recurringTransactions = RecurringTransactions::findOrfail($recurringId);

        $recurringTransactions->delete();

        return redirect(
            route(
                'recurring-list',
                [
                    'account' => $bankAccount,
                    'flow' => $accountFlow
                ]
            )
        );
    }
}
