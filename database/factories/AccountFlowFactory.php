<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AccountFlow;
use Faker\Generator as Faker;

$factory->define(AccountFlow::class, function (Faker $faker) {
    return [
        'label' => ucfirst($faker->words(mt_rand(1, 3))),
        'negative_flow' => $faker->boolean(),
        'account_id' => App\BankAccount::all()->random(),
    ];
});
