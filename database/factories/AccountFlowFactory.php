<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AccountFlow;
use Faker\Generator as Faker;

$factory->define(AccountFlow::class, function (Faker $faker) {
    return [
        'label' => ucfirst($faker->words(mt_rand(1, 3), true)),
        'negative_flow' => $faker->boolean(),
        'account_id' => App\Models\BankAccount::all()->random(),
    ];
});
