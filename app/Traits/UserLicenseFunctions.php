<?php

namespace App\Traits;

use App\Models\Advisor;
use App\Models\Business;
use App\Models\LicensesForAdvisors;

trait UserLicenseFunctions
{

    public function licenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id');
    }

    public function activeLicenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id')
            ->where('active', true);
    }

    public function notActiveLicenses()
    {
        return $this->belongsToMany(Business::class, 'licenses', 'advisor_id', 'business_id')
            ->where('active', false);
    }

    public function assignLicense($business)
    {
        $this->licenses()->sync($business, false);
    }

    public function advisorsLicenses()
    {
        return $this->hasMany(
            LicensesForAdvisors::class,
            'advisor_id',
            'id');
    }

    /**
     * Returns the current maximum license an advisor user can hold
     *
     * @return int|boolean
     */
    public function getSeatsAttribute()
    {
        if ($this->isAdvisor()) {
            return Advisor::firstWhere('user_id', $this->id)->seats;
        }

        return false;
    }

    /**
     * Get the advisors niche
     *
     * Note:Currently unused
     * An advisor may have a specialised Niche, eg. an advisor may
     * specialise in looking after hospitality based businesses.
     *
     * @return string|boolean
     */
    public function getNicheAttribute()
    {
        if ($this->isAdvisor()) {
            return Advisor::firstWhere('user_id', $this->id)->niche;
        }

        return false;
    }

    /**
     * Returns the tier ranking of the current user
     *
     * Note:Currently unused. Tier is meant to reresent a
     * value of an advisor, eg. Gold, Silver and Bronze
     *
     * This will either be based ona future pricing system or
     * a metric based ranking system.
     *
     * @return string|null|boolean
     */
    public function getTierAttribute()
    {
        if ($this->isAdvisor()) {
            return Advisor::firstWhere('user_id', $this->id)->tier;
        }

        return false;
    }
}
