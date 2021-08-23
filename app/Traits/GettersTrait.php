<?php

namespace App\Traits;


use App\Models\BankAccount;
use App\Models\AccountFlow;
use App\Models\Business;
use Illuminate\Support\Facades\Cache;

trait GettersTrait
{
    private function getBankAccount($accountId)
    {
        $key = 'BankAccount_'.$accountId;

        $bankAccount = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($bankAccount === null) {
            $bankAccount = BankAccount::find($accountId);

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $bankAccount, now()->addMinutes(10));
            }
        }

        return $bankAccount;
    }

    private function getFlowAccount($accountId)
    {
        $key = 'FlowAccount_'.$accountId;

        $flowAccount = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($flowAccount === null) {
            $flowAccount = AccountFlow::find($accountId);

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $flowAccount, now()->addMinutes(10));
            }
        }

        return $flowAccount;
    }

    public function getBusinessAll()
    {
        $key = 'Business_all';
        $businesses = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($businesses === null) {
            $businesses = Business::all();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $businesses, now()->addMinutes(10));
            }
        }

        return $businesses;
    }

}
