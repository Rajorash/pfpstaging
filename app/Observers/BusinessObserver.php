<?php

namespace App\Observers;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Phase;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;

class BusinessObserver
{

    /**
     * Function to run upon successful creation of a business
     *
     */
    public function created(Business $business)
    {
        $this->initialisePhases($business);
        $this->initialiseAccounts($business);

        if (\Config::get('app.pfp_cache')) {
            // clear the cached all businesses object
            Cache::forget('Business_all');
        }
    }

    public function updated(Business $business)
    {
        if (\Config::get('app.pfp_cache')) {
            // clear the cached all businesses object
            Cache::forget('Business_all');
        }

        if ($business->rollout) {
            $phase_index = 1;
            foreach ($business->rollout as $phase) {
                $phase->start_date = Carbon::parse($business->start_date)->addMonths(3 * ($phase_index - 1));
                $phase->end_date = Carbon::parse($business->start_date)->addMonths(3 * $phase_index)->addDay(-1);
                $phase->save();
                $phase_index++;
            }
        }
    }

    public function deleted(Business $business)
    {
        if (\Config::get('app.pfp_cache')) {
            // clear the cached all businesses object
            Cache::forget('Business_all');
        }
    }

    /**
     * On business creation, initialise default phase setup.
     *
     * @param [type] $business
     * @return void
     */
    private function initialisePhases($business)
    {
        // create empty phases and assign to the business, each 3 months apart on end_date
        for ($phase_index = 1; $phase_index <= Phase::DEFAULT_PHASE_COUNT; $phase_index++) {
            # code...
            $phase = new Phase;
            $phase->business_id = $business->id;
            $phase->start_date = Carbon::parse($business->start_date)->addMonths(3 * ($phase_index - 1));
            $phase->end_date = Carbon::parse($business->start_date)->addMonths(3 * $phase_index)->addDay(-1);
            $phase->save();
        }

    }

    /**
     * On business creation, initialise default account setup.
     *
     * @param [type] $business
     * @return void
     */
    private function initialiseAccounts($business)
    {
        $accounts = $this->getDefaultAccounts();

        foreach ($accounts as $acc) {
            $account = factory(BankAccount::class)->create([
                'name' => $acc['name'],
                'type' => $acc['type'],
                'business_id' => $business->id
            ]);
            $business->accounts()->save($account);

            foreach ($acc['flows'] as $flow) {
                $new_flow = factory(AccountFlow::class)->create([
                    'label' => $flow['label'],
                    'negative_flow' => $flow['negative']
                ]);
                $account->flows()->save($new_flow);
            }
        }
    }

    private function getDefaultAccounts(): array
    {
        return array(
            [
                'name' => 'Revenue',
                'type' => 'revenue',
                'flows' => [
                    ['label' => "Accounts Receivable", 'negative' => false],
                    ['label' => "Estimated Activity", 'negative' => false],
                ]
            ],
            [
                'name' => 'Profit',
                'type' => 'postreal',
                'flows' => [
                    ['label' => "Distributions", 'negative' => true],
                    ['label' => "Debt pay down", 'negative' => true],
                ]

            ],
            [
                'name' => 'Owners Pay',
                'type' => 'postreal',
                'flows' => [
                    ['label' => "Wages", 'negative' => true],
                    ['label' => "Downturn", 'negative' => true],
                ]

            ],
            [
                'name' => 'Mats & Subs',
                'type' => 'prereal',
                'flows' => [
                    ['label' => "Purchases", 'negative' => true],
                    ['label' => "Subcontractors", 'negative' => true],
                ]
            ],
            [
                'name' => 'Opex',
                'type' => 'postreal',
                'flows' => [
                    ['label' => "Rent", 'negative' => true],
                    ['label' => "Education & Training", 'negative' => true],
                    ['label' => "Promotions", 'negative' => true],
                    ['label' => "Software Expenses", 'negative' => true],
                    ['label' => "Website", 'negative' => true],
                    ['label' => "Consulting/Accounting", 'negative' => true],
                    ['label' => "Interest Expenses", 'negative' => true],
                    ['label' => "Bank Fees", 'negative' => true],
                    ['label' => "Insurance", 'negative' => true],
                    ['label' => "General Monthly Costs", 'negative' => true],
                ]
            ],
            [
                'name' => 'Tax',
                'type' => 'postreal',
                'flows' => [
                    ['label' => "BAS - Current", 'negative' => true],
                    ['label' => "Super Payments", 'negative' => true],
                    ['label' => "Payment Plans", 'negative' => true],
                ]
            ],
            [
                'name' => 'G.S.T.',
                'type' => 'salestax',
                'flows' => [
                    ['label' => "BAS - Payment", 'negative' => true],
                ]
            ],
        );
    }
}

