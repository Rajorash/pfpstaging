<?php

namespace App\Traits;


use App\Models\BankAccount;
use App\Models\AccountFlow;
use App\Models\Business;
use Illuminate\Support\Facades\Cache;

trait GettersTrait
{
    private function getBackAccount($accountId)
    {
        $key = 'BankAccount_'.$accountId;

        $bankAccount = Cache::get($key);

        if ($bankAccount === null) {
            $bankAccount = BankAccount::find($accountId);
            Cache::put($key, $bankAccount);
        }

        return $bankAccount;
    }

    private function getFlowAccount($accountId)
    {
        $key = 'FlowAccount_'.$accountId;

        $flowAccount = Cache::get($key);

        if ($flowAccount === null) {
            $flowAccount = AccountFlow::find($accountId);
            Cache::put($key, $flowAccount);
        }

        return $flowAccount;
    }

    private function getBusinessAll()
    {
        $key = 'Business_all';
        $businesses = Cache::get($key);

        if ($businesses === null) {
            $businesses = Business::all();
            Cache::put($key, $businesses);
        }

        return $businesses;
    }

}
