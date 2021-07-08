<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
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
});

    // /**
    //  * Indicate that the user should have a personal team.
    //  *
    //  * @return $this
    //  */
    // public function withPersonalTeam()
    // {
    //     return $this->has(
    //         Team::factory()
    //             ->state(function (array $attributes, User $user) {
    //                 return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
    //             }),
    //         'ownedTeams'
    //     );
    // }
// }
