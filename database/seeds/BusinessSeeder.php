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
        factory(App\Business::class, 10)->create();
        
        factory(App\Business::class)->create([
            'name' => 'Clients Company',
            'owner_id' => 2
        ]);
        
        factory(App\Business::class)->create([
            'name' => 'Advisors Company',
            'owner_id' => 1
        ]);
    }
}
