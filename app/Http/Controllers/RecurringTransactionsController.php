<?php

namespace App\Http\Controllers;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\RecurringTransactions;
use Illuminate\Database\Eloquent\Collection;

class RecurringTransactionsController extends Controller
{
    protected RecurringPipelineController $recurringPipelineController;

    public function __construct()
    {
        $this->recurringPipelineController = new RecurringPipelineController();
    }

//    /**
//     * @param  int  $bankAccountId
//     * @param  int  $accountFlowId
//     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
//     */
//    public function list(int $bankAccountId, int $accountFlowId)
//    {
//        $bankAccount = BankAccount::findOrfail($bankAccountId);
//        $accountFlow = AccountFlow::findOrfail($accountFlowId);
//
//        $recurringTransactions = RecurringTransactions::where('account_id', $accountFlow->id)
//            ->orderBy('title')
//            ->get();
//
//        return view(
//            'recurring.list',
//            [
//                'bankAccount' => $bankAccount,
//                'accountFlow' => $accountFlow,
//                'recurringTransactions' => $recurringTransactions
//            ]
//        );
//    }

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
        return $this->recurringPipelineController->getAllFlowsForecasts(
            $recurringTransactionsArray,
            $dateStart,
            $dateEnd
        );
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
        return $this->recurringPipelineController->getForecast(
            $recurringTransactions,
            $periodDateStart,
            $periodDateEnd
        );
    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
//    public function create(
//        int $bankAccountId,
//        int $accountFlowId
//    ) {
//        $bankAccount = BankAccount::findOrfail($bankAccountId);
//        $accountFlow = AccountFlow::findOrfail($accountFlowId);
//        $recurringTransactions = null;
//
//        return view(
//            'recurring.create',
//            [
//                'bankAccount' => $bankAccount,
//                'accountFlow' => $accountFlow,
//                'recurringTransactions' => $recurringTransactions
//            ]
//        );
//    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @param  int  $recurringId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
//    public function edit(
//        int $bankAccountId,
//        int $accountFlowId,
//        int $recurringId
//    ) {
//        $bankAccount = BankAccount::findOrfail($bankAccountId);
//        $accountFlow = AccountFlow::findOrfail($accountFlowId);
//        $recurringTransactions = RecurringTransactions::findOrfail($recurringId);
//
//        return view(
//            'recurring.create',
//            [
//                'bankAccount' => $bankAccount,
//                'accountFlow' => $accountFlow,
//                'recurringTransactions' => $recurringTransactions
//            ]
//        );
//    }

    /**
     * @param  int  $bankAccountId
     * @param  int  $accountFlowId
     * @param  int  $recurringId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
//    public function delete(
//        int $bankAccountId,
//        int $accountFlowId,
//        int $recurringId
//    ) {
//        $bankAccount = BankAccount::findOrfail($bankAccountId);
//        $accountFlow = AccountFlow::findOrfail($accountFlowId);
//        $recurringTransactions = RecurringTransactions::findOrfail($recurringId);
//
//        $recurringTransactions->delete();
//
//        return redirect(
//            route(
//                'recurring-list',
//                [
//                    'account' => $bankAccount,
//                    'flow' => $accountFlow
//                ]
//            )
//        );
//    }
}
