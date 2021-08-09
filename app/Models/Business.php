<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Business extends Model
{
    use SoftDeletes;

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

    /**
     * Helper function to retrieve advisor for a business.
     * Returns the User model of the advisor if a license is set on
     * the business.
     *
     * If the license is not set, then returns null.
     *
     * @return User|null;
     */
    public function getAdvisorAttribute() {
        return optional($this->license)->advisor;
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

    /**
     * return the active phase by a given date
     *
     * @param $date
     * @return null|int $phase_id
     */
    public function getPhaseIdByDate($date)
    {
        $key = 'getPhaseIdByDate_'.$this->id.'_'.$date;
        $phase = Cache::get($key);

        if ($phase === null) {
            $phase = $this->rollout()->where('end_date', '>=', $date)->first();

            // if the final phase has already expired, default back to phase
            // with the latest end date
            if (empty($phase)) {
                $phase = $this->rollout()->sortBy('end_date')->last();
            }

            Cache::put($key, $phase, now()->addHours(1));
        }

        return $phase ? $phase->id : null;
    }

    /**
     * Utility function to get current phase, uses getPhaseIdByDate() with
     * parameter of todays date
     *
     * Can be called as a property on a Business model instance
     *
     * Usage: Business::first()->current_phase
     * Output: 2 (example business phase id)
     *
     * @return null|int $phase_id
     */
    public function getCurrentPhaseAttribute()
    {
        return $this->getPhaseIdByDate( today() );
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
            Cache::put($key, $account, now()->addMinutes(10));
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
            Cache::put($key, $accountIds, now()->addMinutes(10));
        }

        return $accountIds;
    }
}
