<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

        return Allocation::whereIn('phase_id', $phaseIds)->get();
    }

    public function getPhaseIdByDate($date)
    {
        $phase = $this->rollout()->where('end_date', '>=', $date)->first();

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
        $account = $this->accounts()->where('type', '=', $accountType)->first();

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
        $accountIds = $this->accounts()->where('type', '=', $accountType)->pluck('id')->toArray();

        return $accountIds;
    }
}
