<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LicensesForAdvisors;
use Illuminate\Database\Eloquent\Factories\Factory;


$factory->define(LicensesForAdvisors::class, function () {
    return [
        'advisor_id' => factory(\App\Models\User::class),
        'regional_admin_id' => factory(\App\Models\User::class),
        'licenses' => LicensesForAdvisors::DEFAULT_LICENSES_COUNT
    ];
});
