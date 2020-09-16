<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\License;
use Faker\Generator as Faker;

$factory->define(License::class, function (Faker $faker) {
    return [
        'account_number' => $faker->bothify('**####-####-###'),
        'business_id' => factory(\App\Business::class),
        'advisor_id' => factory(\App\User::class),
        'active' => true
    ];
});
