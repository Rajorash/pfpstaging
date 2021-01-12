<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Business;
use Faker\Generator as Faker;

$factory->define(Business::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'owner_id' => factory(\App\Models\User::class)
    ];
});
