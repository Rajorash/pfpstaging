<?php

namespace App\Traits;

use App\Models\BankAccount;
use App\Models\AccountFlow;
use App\Models\Business;
use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Storage;

trait GettersTrait
{
     
    public function __construct()
    {
        ini_set('memory_limit', -1);
    }

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

        // dd($businesses);

        if ($businesses === null) {
            $businesses = Business::with(
                'owner',
                'license',
                'collaboration',
                'license.advisor',
                'accounts',
                'accounts.flows',
                'rollout'
            )
                ->withCount('accounts')
                ->get();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $businesses, now()->addMinutes(10));
            }
        // dd($businesses,"pulkti");

        }

        return $businesses;
    }

    // public function getBusinessAll()
    // {
    //     $key = 'file.php';
    //     $businesses = \Storage::disk('local')->get($key)? trim(Storage::disk('local')->get($key)) : null;

    //     // dd($businesses,"omsairam");

    //     if ($businesses === null) {
    //         $businesses = Business::with(
    //             'owner',
    //             'license',
    //             'collaboration',
    //             'license.advisor',
    //             'accounts',
    //             'accounts.flows',
    //             'rollout'
    //         )
    //             ->withCount('accounts')
    //             ->get();

    //         // if (\Storage::disk('local')->get($key)) {
    //             Storage::disk('local')->put($key, $businesses, now()->addMinutes(10));
    //             // Cache::put($key, $businesses, now()->addMinutes(10));
    //         //     die("IN");
    //         // }
    //         // die("OUT");

    //     }

    //     return $businesses;
    // }

}
