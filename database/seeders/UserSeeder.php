<?php

namespace Database\Seeders;

use App\Models\Advisor as Advisor;
use App\Models\Role as Role;
use App\Models\User as User;
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
            'email' => 'advisor@pfp.com'
        ]);
        $advisor_role = Role::where('name', 'advisor')->first();
        $advisor->assignRole($advisor_role);
        $advisor->email_verified_at = now();
        $advisor->remember_token = Str::random(10);
        $advisor->password = Hash::make('letmeinnow!');
        $advisor->save();
        $advisor_details = new Advisor($advisor->id);
        $advisor_details->save();

        $client = User::firstOrCreate([
            'name' => 'Test Client',
            'email' => 'client@pfp.com'
        ]);
        $client_role = Role::where('name', 'client')->first();
        $client->assignRole($client_role);
        $client->email_verified_at = now();
        $client->remember_token = Str::random(10);
        $client->password = Hash::make('letmeinnow!');
        $client->save();

        $advisor2 = User::firstOrCreate([
            'name' => 'Craig Minter',
            'email' => 'craig@mintscdconsulting.com.au'
        ]);
        $advisor2->assignRole($advisor_role);
        $advisor2->email_verified_at = now();
        $advisor2->remember_token = Str::random(10);
        $advisor2->password = Hash::make('CML9Zy!&$H2#e@e9');
        $advisor2->save();
        $advisor2_details = new Advisor($advisor2->id);
        $advisor2_details->save();

        $superadmin = User::firstOrCreate([
            'name' => 'SuperAdmin',
            'email' => 'superadmin@pfp.com'
        ]);
        $superadmin_role = Role::where('name', 'superuser')->first();
        $superadmin->assignRole($superadmin_role);
        $superadmin->email_verified_at = now();
        $superadmin->remember_token = Str::random(10);
        $superadmin->password = Hash::make('#j$dZW|bdYO+`CW`~,y|');
        $superadmin->save();
    }
}
