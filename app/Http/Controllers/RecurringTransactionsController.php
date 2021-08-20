<?php

namespace App\Http\Controllers;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\RecurringTransactions;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    /**
     * @param  RecurringTransactions  $recurringTransactions
     * @return array
     */
    public function getForecast(RecurringTransactions $recurringTransactions): array
    {
        $startDate = Carbon::parse($recurringTransactions->date_start);
        $endDate = $recurringTransactions->date_end
            ? Carbon::parse($recurringTransactions->date_end)
            : (
            $recurringTransactions->repeat_every_type
                ? Carbon::now()->addYears(3)
                : Carbon::now()->addMonths(3));

        $days = [];
        if (isset($recurringTransactions->repeat_rules['days'])) {
            foreach ($recurringTransactions->repeat_rules['days'] as $dayName) {
                $this->_getDaysByName($days,
                    $dayName,
                    $recurringTransactions->repeat_every_number,
                    $recurringTransactions->repeat_every_type,
                    $startDate,
                    $endDate,
                    $recurringTransactions->value
                );
            }
        } else {
            $this->_getDaysByName($days,
                null,
                $recurringTransactions->repeat_every_number,
                $recurringTransactions->repeat_every_type,
                $startDate,
                $endDate,
                $recurringTransactions->value
            );
        }

        ksort($days);

        return $days;
    }

    /**
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
    public function create(int $bankAccountId, int $accountFlowId)
    {
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
    public function edit(int $bankAccountId, int $accountFlowId, int $recurringId)
    {
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
    public function delete(int $bankAccountId, int $accountFlowId, int $recurringId)
    {
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
