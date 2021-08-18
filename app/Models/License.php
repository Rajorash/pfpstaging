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

    /**
     * Return the Regional Admin relationship. The Regional Admin is the user who created and provisioned the
     * license to the advisor (if assigned)
     *
     * @return void
     */
//    public function admin()
//    {
//        return $this->hasOne(User::class, 'id', 'regionaladmin_id');
//    }

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
//        $this->assigned_ts = Carbon::now();
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
