<?php

namespace App\Traits;

trait UserLicenseFunctions
{
    public int $max_licenses;

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
}
