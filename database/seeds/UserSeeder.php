<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $advisor = App\User::firstOrCreate([
            'name' => 'Test Advisor',
            'email' => 'advisor@pfp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('letmeinnow!'),
            'remember_token' => Str::random(10)
        ]);
        $advisor_role = App\Role::where('name', 'advisor')->first();
        $advisor->assignRole($advisor_role);
        
        $client = App\User::firstOrCreate([
            'name' => 'Test Client',
            'email' => 'client@pfp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('letmeinnow!'),
            'remember_token' => Str::random(10)
        ]);
        $client_role = App\Role::where('name', 'client')->first();
        $client->assignRole($client_role);



    }
}
        