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
        factory(App\Business::class)
            ->create()
            ->each( function ($business) {
                $accounts = factory(App\BankAccount::class, 5)->create([
                    'business_id' => $business->id
                ]);
                $business->accounts()->saveMany($accounts);
            $license = factory(App\License::class)->make([
                'business_id' => $business->id,
                'advisor_id' => 1,
            ]);
            $business->license()->save($license);
    });
        





        // factory(App\Business::class)->create([
        //     'name' => 'Clients Company',
        //     'owner_id' => 2
        // ])->each( function ( $business ) {
        //     $license = factory(App\License::class)->make([
        //         'business_id' => $business->id,
        //         'advisor_id' => 1,
        //     ]);
        //     $business->license()->save($license);
        // });
        
    }
}
