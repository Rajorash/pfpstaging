<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Advisor;
use Faker\Generator as Faker;

$factory->define(Advisor::class, function (Faker $faker) {
    $tiers = ['bronze', 'silver', 'gold'];
    return [
        'id' => factory(\App\Models\User::class),
        'seat_limit' => 5,
        'niche' => $faker->jobTitle,
        'tier' => $tiers[mt_rand(0, count($tiers) - 1)],
    ];
});
