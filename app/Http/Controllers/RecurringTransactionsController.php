<?php

namespace App\Http\Controllers;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\RecurringTransactions;
use Illuminate\Http\Request;

class RecurringTransactionsController extends Controller
{
    //

    public function list($bankAccountId, $accountFlowId)
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

    public function create($bankAccountId, $accountFlowId)
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

    public function edit($bankAccountId, $accountFlowId, $recurringId)
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

    public function delete($bankAccountId, $accountFlowId, $recurringId)
    {
        $bankAccount = BankAccount::findOrfail($bankAccountId);
        $accountFlow = AccountFlow::findOrfail($accountFlowId);
        $recurringTransactions = RecurringTransactions::findOrfail($recurringId);

        $recurringTransactions->delete();

        return redirect(route('recurring-list',
            [
                'account' => $bankAccount,
                'flow' => $accountFlow
            ]
        ));
    }
}
