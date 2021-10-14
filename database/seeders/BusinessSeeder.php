<?php

namespace Database\Seeders;

use App\Interfaces\RoleInterface;
use App\Models\User as User;
use App\Models\Business as Business;
use App\Models\License as License;
use App\Models\Role as Role;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $test_client = User::whereEmail('client@pfp.com')->first('id');
        $test_advisor = User::whereEmail('advisor@pfp.com')->first('id');
        $craig_account = User::whereEmail('craig@mintscdconsulting.com.au')->first('id');

        factory(Business::class)->create([
            'name' => 'Clients Company',
            'owner_id' => $test_client->id
        ])->each( function ( $new_business ) use ( $test_advisor ) {
            $this->setLicense( $new_business, $test_advisor);
        });

        factory(Business::class)->create([
            'name' => 'Craig\'s Client Company',
            'owner_id' => $test_client->id
        ])->each( function ( $new_business ) use ( $craig_account ) {
            $this->setLicense( $new_business, $craig_account);
        });

        factory(Business::class, 2)->create([
            'owner_id' => User::factory()->create()->id
        ])->each(
            function ($business) use ( $test_advisor )  {
                $this->setLicense($business, $test_advisor);
                // assign client role to generated business owners
                $client_role = Role::firstWhere('name', RoleInterface::ROLE_CLIENT);
                $business->owner->assignRole($client_role);
            }
        );

    }

    private function setLicense($business, $test_advisor)
    {
        // generate license, assign business to advisor with id of 1
        $license = factory(License::class)->make([
            'business_id' => $business->id,
            'advisor_id' => $test_advisor->id,
        ]);
        $business->license()->save($license);

    }
}

