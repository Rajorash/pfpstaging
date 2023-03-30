<?php

namespace App\Models;

use App\Traits\Allocatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\BankAccount
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $business_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Allocation[] $allocations
 * @property-read int|null $allocations_count
 * @property-read \App\Models\Business $business
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccountFlow[] $flows
 * @property-read int|null $flows_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AllocationPercentage[] $percentages
 * @property-read int|null $percentages_count
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankAccount extends Model
{
    use Allocatable;

    const ACCOUNT_TYPE_REVENUE = 'revenue';
    const ACCOUNT_TYPE_PRETOTAL = 'pretotal';
    const ACCOUNT_TYPE_SALESTAX = 'salestax';
    const ACCOUNT_TYPE_PREREAL = 'prereal';
    const ACCOUNT_TYPE_POSTREAL = 'postreal';

    protected $fillable = ['name', 'type'];
    protected $with = ['flows'];

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function flows(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AccountFlow::class, 'account_id')->orderBy('flow_position', 'ASC');
    }

    public function percentages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AllocationPercentage::class, 'bank_account_id');
    }

    public static function type_list(): array
    {
        return [
            1 => self::ACCOUNT_TYPE_REVENUE,
            2 => self::ACCOUNT_TYPE_PRETOTAL,
            3 => self::ACCOUNT_TYPE_SALESTAX,
            4 => self::ACCOUNT_TYPE_PREREAL,
            5 => self::ACCOUNT_TYPE_POSTREAL
        ];
    }


//    public function allocations()
//    {
//        return $this->morphMany('App\Models\Allocation', 'allocatable');
//    }

    /**
     * @param  null  $phase_id
     * @return mixed
     */
    public function getAllocationPercentages($phase_id = null)
    {
        $key = 'getAllocationPercentages_'.$phase_id.'|'.$this->id;

        $getAllocationPercentages = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($getAllocationPercentages === null) {
            if ($phase_id) {
                $getAllocationPercentages = AllocationPercentage
                    ::where('bank_account_id', '=', $this->id)
                    ->where('phase_id', '=', $phase_id)
                    ->get();
            } else {
                $getAllocationPercentages = AllocationPercentage
                    ::where('bank_account_id', '=', $this->id)
                    ->get();
            }

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $getAllocationPercentages);
            }
        }

        return $getAllocationPercentages;
    }

    /**
     * Get all percentages for the current business on given phase
     *
     * @param $phaseId integer
     * @return array
     *  [<account_type> =>
     *      [
     *          <account_id> => <percentage_value>
     *      ]
     *  ]
     */
    public function getAllAllocationPercentages(int $phaseId): ?array
    {
        $key = 'getAllAllocationPercentages_'.$phaseId.'_'.$this->business_id;

        $getAllAllocationPercentages = \Config::get('app.pfp_cache') ? Cache::get($key) : null;

        if ($getAllAllocationPercentages === null) {
            $getAllAllocationPercentages = BankAccount::where('business_id', $this->business_id)
                ->with('percentages', function ($query) use ($phaseId) {
                    return $query->where('phase_id', $phaseId);
                })
                ->get()
                ->mapToGroups(function ($item, $key) {
                    return [
                        $item->type => [
                            'id' => $item->id,
                            'val' => count($item->percentages) ? $item->percentages[0]->percent : null
                        ]
                    ];
                })->map(function ($a_item) {
                    return array_column(collect($a_item)->toArray(), 'val', 'id');
                })->toArray();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $getAllAllocationPercentages, now()->addMinutes(10));
            }
        }

        return $getAllAllocationPercentages;
    }

    /**
     * Return the sum of allocations for the given date and current account
     *
     * @param $date
     * @param $phaseId
     * @return mixed
     */
    public function getAllocationsTotalByDate($date, $phaseId)
    {
        $key = 'getAllocationsTotalByDate_'.$date.'_'.$phaseId;

//        $getAllocationsTotalByDate = Cache::get($key);

//        if ($getAllocationsTotalByDate === null) {
        $getAllocationsTotalByDate = AccountFlow::where('account_id', $this->id)
            ->with('allocations', function ($query) use ($date, $phaseId) {
                return $query->where('allocation_date', $date)
                    ->where('phase_id', $phaseId);
            })
            ->get()
            ->map(function ($item) {
                return collect($item->toArray())
                    ->only('negative_flow', 'allocations')
                    ->all();
            })
            ->map(function ($a_item) {
                return count($a_item['allocations']) > 0
                    ? $a_item['negative_flow']
                        ? $a_item['allocations'][0]['amount'] * -1
                        : $a_item['allocations'][0]['amount']
                    : 0;
            })->sum();
//            Cache::put($key, $getAllocationsTotalByDate);
//        }

        return $getAllocationsTotalByDate;
    }

    /**
     * Get revenue (income) for whole business on a given date
     *
     * @param $businessId integer
     * @param $date       string
     * @return mixed
     */
    public function getRevenueByDate(int $businessId, string $date)
    {
        $key = 'getRevenueByDate_'.$businessId.'_'.$date;
        $getRevenueByDate = Cache::get($key);

        if ($getRevenueByDate === null) {
            $getRevenueByDate = self::where('type', 'revenue')->where('business_id', $businessId)
                ->with('allocations', function ($query) use ($date) {
                    return $query->where('allocation_date', $date);
                })
                ->get()
                ->map(function ($item) {
                    return collect($item->toArray())
                        ->only('allocations')
                        ->all();
                })
                ->map(function ($a_item) {
                    return count($a_item['allocations']) > 0
                        ? $a_item['allocations'][0]['amount']
                        : 0;
                })->sum();

            if (\Config::get('app.pfp_cache')) {
                Cache::put($key, $getRevenueByDate, now()->addMinutes(10));
            }
        }

        return $getRevenueByDate;
    }

    /**
     * Get the amount on Transfer In for different account types
     *
     * @param $date     string
     * @param $phase_id integer
     * @return float|int
     */
    public function getTransferAmount(string $date, int $phase_id)
    {
        $percents = $this->getAllAllocationPercentages($phase_id);
        $income = $this->getRevenueByDate($this->business_id, $date);
        $amount = 0;

        // NSP = $income / ($percents['salestax'][<account id>] / 100 + 1)
        // Tax amount = $income - NSP
        switch ($this->type) {
            case self::ACCOUNT_TYPE_SALESTAX: // Tax amt
                $amount = ($income > 0)
                    ? round($income - $income / ($percents[$this->type][$this->id] / 100 + 1), 4)
                    : 0;
                break;

            case self::ACCOUNT_TYPE_PRETOTAL:
                $salestax = data_get($percents, self::ACCOUNT_TYPE_SALESTAX);
                $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
                $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;
                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round($nsp * ($percents[$this->type][$this->id] / 100), 4)
                    : 0;
                break;

            case self::ACCOUNT_TYPE_PREREAL:
                $prereal = $this->getPrePrereal($income, $percents);

                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round($prereal * ($percents[$this->type][$this->id] / 100), 4)
                    : 0;
                break;

            case self::ACCOUNT_TYPE_POSTREAL:
                $prereal = $this->getPrePrereal($income, $percents);
                $prereal_percents = array_sum($percents[self::ACCOUNT_TYPE_PREREAL]);

                // Real Revenue = $prereal - $prereal * ($prereal_percents / 100)
                $amount = (is_numeric($percents[$this->type][$this->id]))
                    ? round(($prereal - $prereal * ($prereal_percents / 100)) * ($percents[$this->type][$this->id] / 100),
                        4)
                    : 0;
                break;
        }

        return $amount;
    }

    /**
     * Get the value of revenue after excluding sales tax and saving to drip account
     *
     * @param $income
     * @param $percents
     * @return float|int
     */
    private function getPrePrereal($income, $percents)
    {
        $salestax = data_get($percents, self::ACCOUNT_TYPE_SALESTAX);
        $salestax = count($salestax) > 0 ? $salestax[key($salestax)] : null;
        $nsp = ($income > 0 && is_numeric($salestax)) ? $income / ($salestax / 100 + 1) : 0;

        $pretotal = data_get($percents, self::ACCOUNT_TYPE_PRETOTAL);
        $pretotal = count($pretotal) > 0 ? $pretotal[key($pretotal)] : null;
        $pretotal_amt = (is_numeric($pretotal))
            ? round($nsp * ($pretotal / 100), 4)
            : 0;

        return $nsp - $pretotal_amt;
    }

//    /**
//     * Return the tax rate for the account (if it has one)
//     */
//    public function taxRate(): \Illuminate\Database\Eloquent\Relations\HasOne
//    {
//        return $this->hasOne(TaxRate::class);
//    }

    /**
     * returns true if the account type should be able to be deleted
     *
     * @return boolean
     */
    public function isDeletable(): bool
    {
        $undeletable_types = [
            self::ACCOUNT_TYPE_REVENUE,
            self::ACCOUNT_TYPE_SALESTAX,
        ];

        if (in_array($this->type, $undeletable_types)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the result of totalling all allocations weighted by the flow certainty for a given date.
     *
     * @param  string|date  $date
     * @return float
     */
    public function getAdjustedFlowsTotalByDate($date): float
    {
        $adjusted_total = 0;
        //TODO: using typical request
        /**
         * $flows = DB::table('allocations AS a')
         * ->join('account_flows AS f', 'a.allocatable_id', '=', 'f.id')
         * ->select('a.id', 'a.allocatable_id', 'a.amount', 'a.allocation_date', 'f.certainty', 'f.negative_flow')
         * ->where('a.allocatable_type', 'like', '%Flow')
         * ->where('f.account_id', '=', $this->id)
         * ->whereDate('a.allocation_date', $date)
         * ->get();
         *
         * foreach ($flows as $flow) {
         * $adjusted_total += ($flow->negative_flow ? -1 : 1) * $flow->amount * ($flow->certainty / 100);
         * }
         */

        //TODO: using mysql procedure
        $adjusted = DB::select('CALL AdjustedFlowsTotalByDate ( '.$this->id.', \''.$date.'\' );');

        foreach ($adjusted as $row) {
            $adjusted_total = floatval($row->suma);
        }

        return $adjusted_total;
    }

    /**
     * Returns the result of totalling all allocations weighted by the flow certainty for a given date.
     *
     * @return array
     */
    public function getAdjustedFlowsTotalByDatePeriod($accountid,$dateStart, $dateEnd): array
    {
        $adjusted_total = [];
        //TODO: using typical request
        /**
         * $flows = DB::table('allocations AS a')
         * ->join('account_flows AS f', 'a.allocatable_id', '=', 'f.id')
         * ->select('a.id', 'a.allocatable_id', 'a.amount', 'a.allocation_date', 'f.certainty', 'f.negative_flow')
         * ->where('a.allocatable_type', 'like', '%Flow')
         * ->where('f.account_id', '=', $this->id)
         * ->whereDate('a.allocation_date', '>=', $dateStart)
         * ->whereDate('a.allocation_date', '<=', $dateEnd)
         * ->get();
         *
         * foreach ($flows as $flow) {
         * if (!isset($adjusted_total[$flow->allocation_date])) {
         * $adjusted_total[$flow->allocation_date] = 0;
         * }
         * $adjusted_total[$flow->allocation_date] += ($flow->negative_flow ? -1 : 1) * $flow->amount * ($flow->certainty / 100);
         * }
         *
         * return $adjusted_total;
         */

        //TODO: using mysql procedure
        $flowTotals = DB::select('CALL AdjustedFlowsTotalByDatePeriod ( '.$accountid.', \''.$dateStart.'\', \''.$dateEnd.'\' );');

        foreach ($flowTotals as $row) {
            $adjusted_total[$row->allocation_date] = floatval($row->suma);
        }

        return $adjusted_total;
    }

    /**
     * returns a Carbon date instance of the last date an account balance was
     * entered for the account
     *
     * @return Carbon
     */
    public function dateOfLastBalanceEntry(): Carbon
    {
        $date = DB::table('allocations')
            ->select('id', 'allocatable_id', 'amount', 'allocation_date')
            ->where('allocatable_type', 'like', '%BankAccount')
            ->where('allocatable_id', '=', $this->id)
            ->where('manual_entry', '=', 1)
            ->max('allocation_date');

        return Carbon::createFromFormat('Y-m-d', $date);
    }

    public function dateOfUpdateBalanceEntry($allocid)
    {
        $date = DB::table('allocations')
            ->select('id', 'allocatable_id', 'amount', 'allocation_date')
            ->where('allocatable_type', 'like', '%BankAccount')
            ->where('allocatable_id', '=', $allocid)
            ->where('manual_entry', '=', 1)
            ->max('allocation_date');

        return  $date ? $date : '';
    }
}
