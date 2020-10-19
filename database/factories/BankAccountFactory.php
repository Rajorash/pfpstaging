<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BankAccount;
use Faker\Generator as Faker;

$factory->define(BankAccount::class, function (Faker $faker) {
    $acc_types = BankAccount::type_list();
    
    return [
        'name' => ucfirst($faker->word()),
        'type' => $acc_types[mt_rand(1, count($acc_types))],
        'business_id' => factory(\App\Business::class),
    ];
});
