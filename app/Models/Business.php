<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Business
 *
 * @property int $id
 * @property string $name
 * @property int|null $owner_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $start_date
 * @property-read Collection|BankAccount[] $accounts
 * @property-read int|null $accounts_count
 * @property-read Collaboration|null $collaboration
 * @property-read User|\App\Models\null; $advisor
 * @property-read null|int $current_phase
 * @property-read License|null $license
 * @property-read User|null $owner
 * @property-read Collection|Phase[] $rollout
 * @property-read int|null $rollout_count
 * @method static Builder|Business newModelQuery()
 * @method static Builder|Business newQuery()
 * @method static \Illuminate\Database\Query\Builder|Business onlyTrashed()
 * @method static Builder|Business query()
 * @method static Builder|Business whereCreatedAt($value)
 * @method static Builder|Business whereDeletedAt($value)
 * @method static Builder|Business whereId($value)
 * @method static Builder|Business whereName($value)
 * @method static Builder|Business whereOwnerId($value)
 * @method static Builder|Business whereStartDate($value)
 * @method static Builder|Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Business withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Business withoutTrashed()
 * @mixin Eloquent
 */
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
    public function getAdvisorAttribute()
    {
        return optional($this->license)->advisor;
    }

    public function allocations()
    {
        $phaseIds = $this->rollout()->pluck('id');

        $key = 'allocations_'.$phaseIds->implode('_');
        $allocations = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($allocations === null) {
            $allocations = Allocation::whereIn('phase_id', $phaseIds)->get();
            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $allocations);
            }
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
        $phase = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($phase === null) {
            $phase = $this->rollout()->where('end_date', '>=', $date)->first();

            // if the final phase has already expired, default back to phase
            // with the latest end date
            if (empty($phase)) {
                $phase = $this->rollout()->sortBy('end_date')->last();
            }

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $phase, now()->addHours(1));
            }
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
        return $this->getPhaseIdByDate(today());
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

        $account = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($account === null) {
            $account = $this->accounts()->where('type', '=', $accountType)->first();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $account, now()->addMinutes(10));
            }
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

        $accountIds = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($accountIds === null) {
            $accountIds = $this->accounts()
                ->where('type', '=', $accountType)
                ->pluck('id')
                ->toArray();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $accountIds, now()->addMinutes(10));
            }
        }

        return $accountIds;
    }
}
