<?php

namespace Database\Seeders;

use App\Models\Permission as Permission;
use App\Models\Role as Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = [
            ['name' => 'see_clients', 'label' => 'View associated clients'],
            ['name' => 'see_client_list', 'label' => 'See a list of clients'],
            ['name' => 'update_client_allocations', 'label' => 'Update Client Allocations'],
            ['name' => 'view_client_allocations', 'label' => 'View Client Allocations'],
            ['name' => 'update_own_allocations', 'label' => 'Update Own Allocations'],
            ['name' => 'view_own_allocations', 'label' => 'View Own Allocations']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        $advisor_role = Role::where('name', 'advisor')->first();
        $advisor_permissions = [
            'see_clients',
            'see_client_list',
            'update_client_allocations',
            'view_client_allocations',
        ];
        foreach ($advisor_permissions as $permission) {
            $advisor_role->allowTo( Permission::where('name', $permission)->first() );
        }

        $client_role = Role::where('name', 'client')->first();
        $client_permissions = [
            'update_own_allocations',
            'view_own_allocations',
        ];
        foreach ($client_permissions as $permission) {
            $client_role->allowTo( Permission::where('name', $permission)->first() );
        }
    }
}
