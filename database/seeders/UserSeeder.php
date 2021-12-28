<?php

namespace Database\Seeders;

use App\Interfaces\RoleInterface;
use App\Models\Advisor as Advisor;
use App\Models\LicensesForAdvisors;
use App\Models\Role as Role;
use App\Models\User as User;
use Carbon\Carbon;
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
        $superadmin = User::whereEmail('craigm@thehealthybusinesslab.com')->first();
        if (!$superadmin) {
            $superadmin = User::factory()->create([
                'name' => 'CraigmSuperAdmin',
                'email' => 'craigm@thehealthybusinesslab.com',
                'password' => Hash::make('PMPounceMarketing2021!'),
                'title' => "Craigm SuperAdmin",
                'responsibility' => "craigm superuser",
            ]);
        }
        $superadmin->assignRole($this->getRole(RoleInterface::ROLE_SUPERADMIN));

//        // Create Superuser account for testing
//        $superadmin = User::whereEmail('superadmin@pfp.com')->first();
//        if (!$superadmin) {
//            $superadmin = User::factory()->create([
//                'name' => 'SuperAdmin',
//                'email' => 'superadmin@pfp.com',
//                'password' => Hash::make('#j$dZW|bdYO+`CW`~,y|'),
//                'title' => "Test SuperAdmin",
//                'responsibility' => "Testing superuser",
//            ]);
//        }
//        $superadmin->assignRole($this->getRole(RoleInterface::ROLE_SUPERADMIN));
//
//        // Create Regional Admin account for testing
//
//        $regionalAdmin = User::whereEmail('regionaladmin@pfp.com')->first();
//        if (!$regionalAdmin) {
//            $regionalAdmin = User::factory()->create([
//                'name' => 'RegionalAdmin',
//                'email' => 'regionaladmin@pfp.com',
//                'password' => Hash::make('#j$dSYUD(W@SbdYO+`CW'),
//                'title' => "Test Admin",
//                'responsibility' => "Testing admin",
//            ]);
//        }
//        $regionalAdmin->assignRole($this->getRole(RoleInterface::ROLE_ADMIN));
//
//        //Advisors
//        $advisor_role = $this->getRole(RoleInterface::ROLE_ADVISOR);
//
//        // Create default advisor account for testing
//        $advisor = User::whereEmail('advisor@pfp.com')->first();
//        if (!$advisor) {
//            $advisor = User::factory()->create([
//                'name' => 'Test Advisor',
//                'email' => 'advisor@pfp.com',
//                'password' => Hash::make('letmeinnow!'),
//                'title' => "Test Advisor",
//                'responsibility' => "Testing advisor",
//            ]);
//        }
//        $advisor->assignRole($advisor_role);
//        $advisor->regionalAdminByAdvisor()->sync($regionalAdmin);
//        $LicensesForAdvisorsTA = factory(LicensesForAdvisors::class)->create(
//            [
//                'advisor_id' => $advisor->id,
//                'regional_admin_id' => $regionalAdmin->id,
//                'licenses' => LicensesForAdvisors::DEFAULT_LICENSES_COUNT
//            ]
//        );
//        $advisor->regionalAdminByAdvisor()->sync($regionalAdmin);
//        $LicensesForAdvisorsTA->regionalAdmin()->associate($regionalAdmin);
//
//        // Create Craig account for testing
//        $craig = User::whereEmail('craig@mintscdconsulting.com.au')->first();
//        if (!$craig) {
//            $craig = User::factory()->create([
//                'name' => 'Craig Minter',
//                'email' => 'craig@mintscdconsulting.com.au',
//                'password' => Hash::make('CML9Zy!&$H2#e@e9'),
//                'title' => "PFP Professional",
//                'responsibility' => "Client Fulfillment",
//            ]);
//        }
//        $craig->assignRole($advisor_role);
//        $craig->regionalAdminByAdvisor()->sync($regionalAdmin);
//
//        $LicensesForAdvisorsCM = factory(LicensesForAdvisors::class)->create(
//            [
//                'advisor_id' => $craig->id,
//                'regional_admin_id' => $regionalAdmin->id,
//                'licenses' => LicensesForAdvisors::DEFAULT_LICENSES_COUNT
//            ]
//        );
//        $craig->regionalAdminByAdvisor()->sync($regionalAdmin);
//        $LicensesForAdvisorsCM->regionalAdmin()->associate($regionalAdmin);
//
//        // Create Test Client account for testing
//        $client = User::whereEmail('client@pfp.com')->first();
//        if (!$client) {
//            $client = User::factory()->create([
//                'name' => 'Test Client',
//                'email' => 'client@pfp.com',
//                'password' => Hash::make('letmeinnow!'),
//                'title' => "Test Client",
//                'responsibility' => "Testing Client A",
//            ]);
//        }
//        $client->assignRole($this->getRole(RoleInterface::ROLE_CLIENT));
//        $client->advisorByClient()->sync($advisor);

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
