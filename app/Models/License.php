<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'account_number',
        'advisor_id',
        'business_id',
        'regionaladmin_id',
        'active',
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
     * @return void
     */
    public function issue(User $advisor)
    {
        $this->advisor_id($advisor->id)->save();
    }

    /**
     * Assigns an empty license to a business.
     *
     * Should be assigned by an advisor user. Will affect
     * available license count
     *
     * @param  Business  $business
     * @return void
     */
    public function assign(Business $business)
    {
        $this->business_id($business->id)->save();
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
//        dd(Carbon::parse($this->expires_ts)->timestamp, Carbon::now()->timestamp, $this->active);
        if (Carbon::parse($this->expires_ts)->timestamp - Carbon::now()->timestamp > 0 && $this->active) {
            return true;
        }

        return false;
    }
}
