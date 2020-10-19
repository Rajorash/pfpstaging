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
        factory(App\Business::class, 5)->create()->each( function ($business) {
            // generate license, assign business to advisor with id of 1
            $license = factory(App\License::class)->make([
                'business_id' => $business->id,
                'advisor_id' => 1,
            ]);
            $business->license()->save($license);
        });
        
        factory(App\Business::class)->create([
            'name' => 'Clients Company',
            'owner_id' => 2
        ])->each( function ( $new_business ) {
            // generate specific accounts for demo
            $predefined_accounts = [
                [
                    'name' => 'Core',
                    'type' => 'revenue',
                    'flows' => [
                        [ 'label' => "Transfer to revenue", 'negative' => false ],
                    ]
                ],
                [
                    'name' => 'Drip Account',
                    'type' => 'pretotal',
                    'flows' => [
                        [ 'label' => "Transfer to revenue", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Materials',
                    'type' => 'prereal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Purchases", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Subcontractors',
                    'type' => 'prereal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Payments", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Profit',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Distributions", 'negative' => true ],
                        [ 'label' => "Debt pay down", 'negative' => true ],
                    ]

                ],
                [
                    'name' => 'Owners Pay',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Wages", 'negative' => true ],
                        [ 'label' => "Downturn", 'negative' => true ],
                    ]

                ],
                [
                    'name' => 'Staff Related',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Payroll", 'negative' => true ],
                        [ 'label' => "Downturn", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Opex',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "Rent", 'negative' => true ],
                        [ 'label' => "Education & Training", 'negative' => true ],
                        [ 'label' => "Promotions", 'negative' => true ],
                        [ 'label' => "Software Expenses", 'negative' => true ],
                        [ 'label' => "Website", 'negative' => true ],
                        [ 'label' => "Consulting/Accounting", 'negative' => true ],
                        [ 'label' => "Interest Expenses", 'negative' => true ],
                        [ 'label' => "Bank Fees", 'negative' => true ],
                        [ 'label' => "Insurance", 'negative' => true ],
                        [ 'label' => "General Monthly Costs", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Tax',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "BAS - Current", 'negative' => true ],
                        [ 'label' => "Super Payments", 'negative' => true ],
                        [ 'label' => "Payment Plans", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'G.S.T.',
                    'type' => 'salestax',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                        [ 'label' => "BAS - Payment", 'negative' => true ],
                    ]
                ],
                [
                    'name' => 'Vault',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                    ]
                ],
                [
                    'name' => 'Other',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                    ]
                ],
                [
                    'name' => 'Charity',
                    'type' => 'postreal',
                    'flows' => [
                        [ 'label' => "Transfer in", 'negative' => false ],
                    ]
                ],
            ];

            foreach($predefined_accounts as $acc) {
                $account = factory(App\BankAccount::class)->create([
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'business_id' => $new_business->id
                ]);
                $new_business->accounts()->save($account);
                
                foreach($acc['flows'] as $flow) {
                    $new_flow = factory(App\AccountFlow::class)->create(['label' => $flow['label'], 'negative_flow' => $flow['negative']]);
                    $account->flows()->save($new_flow);
                }
            }

            // generate license, assign business to advisor with id of 1
            $license = factory(App\License::class)->make([
                'business_id' => $new_business->id,
                'advisor_id' => 1,
            ]);
            $new_business->license()->save($license);
        });
        
    }
}
