<?php

namespace Database\Factories;

use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = new \Faker\Generator();

        return [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'title' => null,
            'responsibility' => null,
            'password' => Hash::make('letmeinnow!'),
            'timezone' => 'Australia/Sydney',
            'active' => true, // temp fix until licensing fixed
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

//
///** @var \Illuminate\Database\Eloquent\Factory $factory */
//use App\Models\User;
//use Faker\Generator as Faker;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Str;
//
///*
//|--------------------------------------------------------------------------
//| Model Factories
//|--------------------------------------------------------------------------
//|
//| This directory should contain each of the model factory definitions for
//| your application. Factories provide a convenient way to generate new
//| model instances for testing / seeding your application's database.
//|
//*/
//
//$factory->define(User::class, function (Faker $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'email_verified_at' => now(),
//        'title' => null,
//        'responsibility' => null,
//        'password' => Hash::make('letmeinnow!'),
//        'timezone' => 'Australia/Sydney',
//        'active' => true, // temp fix until licensing fixed
//        'remember_token' => Str::random(10),
//    ];
//});
//
//    // /**
//    //  * Indicate that the user should have a personal team.
//    //  *
//    //  * @return $this
//    //  */
//    // public function withPersonalTeam()
//    // {
//    //     return $this->has(
//    //         Team::factory()
//    //             ->state(function (array $attributes, User $user) {
//    //                 return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
//    //             }),
//    //         'ownedTeams'
//    //     );
//    // }
//// }
