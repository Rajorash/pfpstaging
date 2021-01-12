<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\License;
use Faker\Generator as Faker;

$factory->define(License::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bothify('**####-####-###'),
        'business_id' => factory(\App\Models\Business::class),
        'advisor_id' => factory(\App\Models\User::class),
        'active' => true
    ];
});
