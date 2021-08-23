<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\License
 *
 * @property int $id
 * @property string $account_number
 * @property int $active
 * @property int|null $advisor_id
 * @property int|null $business_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $regionaladmin_id
 * @property \Illuminate\Support\Carbon $issued_ts
 * @property \Illuminate\Support\Carbon|null $assigned_ts
 * @property \Illuminate\Support\Carbon|null $expires_ts
 * @property string|null $revoked_ts
 * @property-read \App\Models\User|null $advisor
 * @property-read \App\Models\Business|null $business
 * @property-read mixed $check_license
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License query()
 * @method static Builder|License whereAccountNumber($value)
 * @method static Builder|License whereActive($value)
 * @method static Builder|License whereAdvisorId($value)
 * @method static Builder|License whereAssignedTs($value)
 * @method static Builder|License whereBusinessId($value)
 * @method static Builder|License whereCreatedAt($value)
 * @method static Builder|License whereExpiresTs($value)
 * @method static Builder|License whereId($value)
 * @method static Builder|License whereIssuedTs($value)
 * @method static Builder|License whereRegionaladminId($value)
 * @method static Builder|License whereRevokedTs($value)
 * @method static Builder|License whereUpdatedAt($value)
 * @mixin Eloquent
 */
class License extends Model
{
    protected $fillable = [
        'account_number',
        'advisor_id',
        'business_id',
        'active',
        'issued_ts',
        'assigned_ts',
        'expires_ts'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issued_ts' => 'datetime',
        'assigned_ts' => 'datetime',
        'expires_ts' => 'datetime',
    ];

    public function advisor()
    {
        return $this->hasOne(User::class, 'id', 'advisor_id');
    }

    public function business()
    {
        return $this->hasOne(Business::class, 'id', 'business_id');
    }

    public function setAccountNumberAttribute()
    {
        $this->attributes['account_number'] = uniqid();
    }

    /**
     * Issues the license to an advisor.
     *
     * Should be issueed by a regional admin user.
     *
     * @param  User  $advisor
     * @return License
     */
    public function issue(User $advisor)
    {
        $this->advisor_id = $advisor->id;
        $this->issued_ts = now();
        $this->save();

        return $this;
    }

    /**
     * Assigns an empty license to a business.
     *
     * Should be assigned by an advisor user. Will affect
     * available license count
     *
     * @param  Business  $business
     * @return License
     */
    public function assign(Business $business, $monthsToAdd = 3)
    {
        $this->fill([
            "business_id" => $business->id,
            "assigned_ts" => now(),
            "expires_ts" => now()->addMonths($monthsToAdd),
            "active" => true
        ])->save();

        return $this;
    }

    /**
     * Used to forcibly remove active status of a license.
     *
     * Contextually different from expiry, and will overrule
     * it.
     *
     * @return void
     */
    public function revoke()
    {
        $this->active = false;
        $this->revoked_ts = Carbon::now();
    }

    public function unRevoke()
    {
        $this->active = true;
        $this->revoked_ts = null;
    }

    /**
     * Extend the expiry date of the license by n months,
     * default value is 3
     *
     * @param  integer  $monthsToAdd
     * @return void
     */
    public function extend($monthsToAdd = 3)
    {
        $this->expires_ts = Carbon::createFromTimestamp($this->expires_ts)->addMonths($monthsToAdd);
    }

    public function getCheckLicenseAttribute()
    {
        if (
            Carbon::parse($this->expires_ts)->timestamp - Carbon::now()->timestamp > 0
            && $this->active
        ) {
            return true;
        }

        return false;
    }
}
