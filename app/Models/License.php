<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JamesMills\LaravelTimezone\Timezone;

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
 * @property-read bool $check_license
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereAdvisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereAssignedTs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereExpiresTs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereIssuedTs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereRegionaladminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereRevokedTs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @mixin \Eloquent
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

    public function advisor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'advisor_id');
    }

    public function business(): \Illuminate\Database\Eloquent\Relations\HasOne
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
        $this->revoked_ts = Carbon::parse(Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s'));
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
    public function extend(int $monthsToAdd = 3)
    {
        $this->expires_ts = Carbon::createFromTimestamp($this->expires_ts)->addMonths($monthsToAdd);
    }

    public function getCheckLicenseAttribute(): bool
    {
        // $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        $today = new Timezone();
        $today = $today->convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
        if (
            Carbon::parse($this->expires_ts)->timestamp - Carbon::parse($today)->timestamp > 0
            && $this->active
        ) {
            return true;
        }

        return false;
    }
}
