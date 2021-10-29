<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Business
 *
 * @property int $id
 * @property string $name
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $start_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BankAccount[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \App\Models\Collaboration|null $collaboration
 * @property-read \App\Models\User|\App\Models\null; $advisor
 * @property-read null|int $current_phase
 * @property-read \App\Models\License|null $license
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pipeline[] $pipelines
 * @property-read int|null $pipelines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Phase[] $rollout
 * @property-read int|null $rollout_count
 * @method static \Illuminate\Database\Eloquent\Builder|Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Business newQuery()
 * @method static \Illuminate\Database\Query\Builder|Business onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Business query()
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Business withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Business withoutTrashed()
 * @mixin \Eloquent
 */
class Business extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collaboration(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Collaboration::class);
    }

    public function license(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(License::class);
    }

    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BankAccount::class)->with(['allocations']);
    }

    public function rollout(): \Illuminate\Database\Eloquent\Relations\HasMany
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
            $phase = $this
                ->rollout()
                ->where('end_date', '>=', $date)
                ->first();

            // if the final phase has already expired, default back to phase
            // with the latest end date
            if (empty($phase)) {
                $phase = $this->rollout()
                    ->orderByDesc('end_date')
                    ->first();
            }

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $phase, now()->addHours(1));
            }
        }

        return $phase ? $phase->id : null;
    }

    /**return phases by period range
     * @param CarbonPeriod $period
     * @return array
     */
    public function getPhasesIdByPeriod(CarbonPeriod $period):array
    {
        $phases = [];
        foreach ($period as $date) {
            $phases[$date->format('Y-m-d')] = $this->getPhaseIdByDate($date->format('Y-m-d'));
        }

        return $phases;
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

    public function pipelines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pipeline::class);
    }


    /**
     * returns the last date an entry occurs for the given business
     * will return null if no entries.
     *
     * @return Carbon|null
     */
    public function dateOfLatestEntry(): ?Carbon
    {
        $account_ids = $this->accounts()->pluck('id')->toArray();

        $flow_ids = DB::table('account_flows')
            ->select('id')
            ->where('account_id', 'in', $account_ids)
            ->get()
            ->pluck('id')
            ->toArray();

        $date = DB::table('allocations')
            ->select('id', 'allocatable_id', 'amount', 'allocation_date')
            ->where(function ($query) use ($account_ids) {
                $query->where('allocatable_type', 'like', '%Account')
                    ->whereIn('allocatable_id', $account_ids);
            })
            ->orWhere(function ($query) use ($flow_ids) {
                $query->where('allocatable_type', 'like', '%Flow')
                    ->whereIn('allocatable_id', $flow_ids);
            })
            ->max('allocation_date');


        return $date
            ? Carbon::createFromFormat('Y-m-d', $date)
            : null;

    }
}
