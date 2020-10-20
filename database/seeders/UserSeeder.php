<?php

namespace Database\Seeders;

use App\Advisor as Advisor;
use App\Role as Role;
use App\User as User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $advisor = User::firstOrCreate([
            'name' => 'Test Advisor',
            'email' => 'advisor@pfp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('letmeinnow!'),
            'remember_token' => Str::random(10)
        ]);
        $advisor_role = Role::where('name', 'advisor')->first();
        $advisor->assignRole($advisor_role);
        $advisor_details = new Advisor($advisor->id);
        $advisor_details->save();
        
        $client = User::firstOrCreate([
            'name' => 'Test Client',
            'email' => 'client@pfp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('letmeinnow!'),
            'remember_token' => Str::random(10)
        ]);
        $client_role = Role::where('name', 'client')->first();
        $client->assignRole($client_role);



    }
}
        