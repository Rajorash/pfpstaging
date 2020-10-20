<?php

namespace Database\Seeders;

use App\Role as Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {

        $roles = [
            ['name'=>'superuser', 'label' => 'Superuser'],
            ['name'=>'admin', 'label' => 'Regional Admin'],
            ['name'=>'advisor', 'label' => 'Advisor'],
            ['name' => 'client', 'label' => 'Client']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
