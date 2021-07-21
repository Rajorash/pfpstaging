<?php

namespace Database\Seeders;

use App\Models\Advisor as Advisor;
use App\Models\LicensesForAdvisors;
use App\Models\Role as Role;
use App\Models\User as User;
use Illuminate\Database\Eloquent\Model;
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
        // Create Superuser account for testing
        $superadmin = factory(User::class)->create([
            'name' => 'SuperAdmin',
            'email' => 'superadmin@pfp.com',
            'password' => Hash::make('#j$dZW|bdYO+`CW`~,y|'),
            'title' => "Test SuperAdmin",
            'responsibility' => "Testing superuser",
        ]);
        $superadmin->assignRole($this->getRole(User::ROLE_SUPERADMIN));

        // Create Regional Admin account for testing
        $regionalAdmin = factory(User::class)->create([
            'name' => 'RegionalAdmin',
            'email' => 'regionaladmin@pfp.com',
            'password' => Hash::make('#j$dSYUD(W@SbdYO+`CW'),
            'title' => "Test Admin",
            'responsibility' => "Testing admin",
        ]);
        $regionalAdmin->assignRole($this->getRole(User::ROLE_ADMIN));

        //Advisors
        $advisor_role = $this->getRole(User::ROLE_ADVISOR);

        // Create default advisor account for testing
        $advisor = factory(User::class)->create([
            'name' => 'Test Advisor',
            'email' => 'advisor@pfp.com',
            'password' => Hash::make('letmeinnow!'),
            'title' => "Test Advisor",
            'responsibility' => "Testing advisor",
        ]);
        $advisor->assignRole($advisor_role);
        $advisor->regionalAdminByAdvisor()->sync($regionalAdmin);
        $LicensesForAdvisorsTA = factory(LicensesForAdvisors::class)->create(
            [
                'advisor_id' => $advisor->id,
                'regional_admin_id' => $regionalAdmin->id,
                'licenses' => LicensesForAdvisors::DEFAULT_LICENSES_COUNT
            ]
        );
        $advisor->regionalAdminByAdvisor()->sync($regionalAdmin);
        $LicensesForAdvisorsTA->regionalAdmin()->associate($regionalAdmin);

        // Create Craig account for testing
        $craig = factory(User::class)->create([
            'name' => 'Craig Minter',
            'email' => 'craig@mintscdconsulting.com.au',
            'password' => Hash::make('CML9Zy!&$H2#e@e9'),
            'title' => "PFP Professional",
            'responsibility' => "Client Fulfillment",
        ]);
        $craig->assignRole($advisor_role);
        $craig->regionalAdminByAdvisor()->sync($regionalAdmin);
        $LicensesForAdvisorsCM = factory(LicensesForAdvisors::class)->create(
            [
                'advisor_id' => $craig->id,
                'regional_admin_id' => $regionalAdmin->id,
                'licenses' => LicensesForAdvisors::DEFAULT_LICENSES_COUNT
            ]
        );
        $craig->regionalAdminByAdvisor()->sync($regionalAdmin);
        $LicensesForAdvisorsCM->regionalAdmin()->associate($regionalAdmin);

        // Create Test Client account for testing
        $client = factory(User::class)->create([
            'name' => 'Test Client',
            'email' => 'client@pfp.com',
            'password' => Hash::make('letmeinnow!'),
            'title' => "Test Client",
            'responsibility' => "Testing Client A",
        ]);
        $client->assignRole($this->getRole(User::ROLE_CLIENT));
        $client->advisorByClient()->sync($advisor);

    }

    /**
     * Helper function to return a Role model. ...no pressure.
     *
     * Parameter should be a role constant from App/Models/User
     * eg. User::ROLE_CLIENT or User::ROLE_ADVISOR.
     *
     * See App/Models/Role for more details.
     *
     * @param  string  $roleConstant
     * @return void
     */
    public function getRole($roleConstant)
    {
        return Role::firstWhere('name', $roleConstant);
    }
}
