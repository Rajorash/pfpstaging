<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Business extends Model
{
    protected $fillable = [];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function collaboration()
    {
        return $this->hasOne(Collaboration::class);
    }

    public function license()
    {
        return $this->hasOne(License::class);
    }

    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function rollout()
    {
        return $this->hasMany(Phase::class);
    }

    public function allocations()
    {
        $phaseIds = $this->rollout()->pluck('id');

        $key = 'allocations_'.$phaseIds->implode('_');
        $allocations = Cache::get($key);

        if ($allocations === null) {
            $allocations = Allocation::whereIn('phase_id', $phaseIds)->get();
            Cache::put($key, $allocations);
        }

        return $allocations;
    }

    public function getPhaseIdByDate($date)
    {
        $key = 'getPhaseIdByDate_'.$date;
        $phase = Cache::get($key);

        if ($phase === null) {
            $phase = $this->rollout()->where('end_date', '>=', $date)->first();
            Cache::put($key, $phase);
        }

        return $phase->id;
    }

    /**
     * Get id of the FIRST account of the given type for the current business
     *
     * @param $accountType string
     * @return integer
     */
    public function getAccountIdByType($accountType)
    {
        $key = 'getAccountIdByType_'.$accountType;

        $account = Cache::get($key);

        if ($account === null) {
            $account = $this->accounts()->where('type', '=', $accountType)->first();
            Cache::put($key, $account);
        }

        return $account->id;
    }

    /**
     * Get IDs of All accounts of the given type for the current business
     *
     * @param $accountType
     * @return array
     */
    public function getAllAccountIdsByType($accountType)
    {
        $key = 'getAllAccountIdsByType_'.$accountType;

        $accountIds = Cache::get($key);

        if ($accountIds === null) {
            $accountIds = $this->accounts()->where('type', '=', $accountType)->pluck('id')->toArray();
            Cache::put($key, $accountIds);
        }

        return $accountIds;
    }
}
