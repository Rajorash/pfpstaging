<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Business;
use App\Models\License;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(License::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bothify('**####-####-###'),
        'regionaladmin_id' => User::firstWhere('email','regionaladmin@pfp.com')->id,
        'business_id' => factory(Business::class),
        'advisor_id' => factory(User::class),
        'active' => true,
        'issued_ts' => now(),
        'assigned_ts' => now(),
        'expires_ts' => Carbon::now()->add('3 months')
    ];
});
