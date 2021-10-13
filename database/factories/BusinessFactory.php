<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Interfaces\RoleInterface;
use App\Models\Business;
use App\Models\Role as Role;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Business::class, function (Faker $faker) {

    return [
        'name' => $faker->company,
    ];
});
