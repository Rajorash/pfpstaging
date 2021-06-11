<?php

namespace Database\Seeders;

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
        // factory(Business::class, 5)->create()->each( function ($business) {
        //     // generate random accounts
        //     $accounts = factory(BankAccount::class, 5)->create(['business_id' => $business->id]);
        //     $business->accounts()->saveMany($accounts);

        //     // generate license, assign business to advisor with id of 1
        //     $license = factory(License::class)->make([
        //         'business_id' => $business->id,
        //         'advisor_id' => 1,
        //     ]);
        //     $business->license()->save($license);
        // });

        $test_client = User::whereEmail('client@pfp.com')->first('id');
        $test_advisor = User::whereEmail('advisor@pfp.com')->first('id');
        $craig_account = User::whereEmail('craig@mintscdconsulting.com.au')->first('id');


        factory(Business::class)->create([
            'name' => 'Clients Company',
            'owner_id' => $test_client->id
        ])->each( function ( $new_business ) use ( $test_advisor ) {
            // generate license, assign business to advisor with id of 1
            $license = factory(License::class)->make([
                'business_id' => $new_business->id,
                'advisor_id' => $test_advisor->id,
            ]);
            $new_business->license()->save($license);
        });


        factory(Business::class)->create([
            'name' => 'Craig\'s Client Company',
            'owner_id' => $test_client->id
            ])->each( function ( $new_business ) use ( $craig_account ) {
            // $craig_id = User::where('name', '=', 'Craig Minter')->id;
            // generate license, assign business to advisor with id of 1
            $license = factory(License::class)->make([
                'business_id' => $new_business->id,
                'advisor_id' => $craig_account->id,
            ]);
            $new_business->license()->save($license);
        });

        factory(Business::class, 5)->create()->each( function ($business) use ( $test_advisor )  {
            // generate license, assign business to advisor with id of 1
            $license = factory(License::class)->make([
                'business_id' => $business->id,
                'advisor_id' => $test_advisor->id,
            ]);
            $business->license()->save($license);

            $client_role = Role::where('name', 'client')->first();
            $business->owner->assignRole($client_role);
        });

    }
}
