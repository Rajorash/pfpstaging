<?php

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
        // factory(App\Business::class, 5)->create()->each( function ($business) {
        //     // generate random accounts
        //     $accounts = factory(App\BankAccount::class, 5)->create(['business_id' => $business->id]);
        //     $business->accounts()->saveMany($accounts);

        //     // generate license, assign business to advisor with id of 1
        //     $license = factory(App\License::class)->make([
        //         'business_id' => $business->id,
        //         'advisor_id' => 1,
        //     ]);
        //     $business->license()->save($license);
        // });
        factory(App\Business::class)->create([
            'name' => 'Clients Company',
            'owner_id' => 2
        ])->each( function ( $new_business ) {
            // generate license, assign business to advisor with id of 1
            $license = factory(App\License::class)->make([
                'business_id' => $new_business->id,
                'advisor_id' => 1,
            ]);
            $new_business->license()->save($license);
        });

        factory(App\Business::class, 5)->create()->each( function ($business) {
            // generate license, assign business to advisor with id of 1
            $license = factory(App\License::class)->make([
                'business_id' => $business->id,
                'advisor_id' => 1,
            ]);
            $business->license()->save($license);
        });
        
    }
}
