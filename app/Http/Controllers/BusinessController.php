<?php

namespace App\Http\Controllers;

use App\Models\Allocation as Allocation;
use App\Models\BankAccount;
use App\Traits\GettersTrait;

//use Auth;
use App\Models\Business;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JamesMills\LaravelTimezone\Facades\Timezone;

class BusinessController extends Controller
{
    use GettersTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businesses = $this->getBusinessAll();
        
        // new code 270623 start
        $currentUser = Auth::user();
        $seat_count = 0;
        foreach($businesses as $bus){
            if($bus->license->advisor_id == $currentUser->id && $currentUser->isAdvisor()){
                if ( is_object($bus->license) ){
                    if(checkLicenseStatus($bus->license->id) == true){
                        $seat_count++;
                    }
                }
            }
        }
      
        $available_seats = $currentUser->seats - $seat_count;
        // new code 270623 end
        
        $filtered = $businesses->filter(function ($business) {
            return Auth::user()->can('view', $business);
        })->values();

        return view(
            'business.list',
            [
                'businesses' => $filtered,
                'currentUser' => Auth::user(),
                'available_seats' => $available_seats,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', $business);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', $business);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Business $business)
    {
        $this->authorize('view', $business);

        return view('accounts.show', ['business' => $business]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Business $business)
    {
        $this->authorize('edit', $business);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Business $business)
    {
        $this->authorize('update', $business);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Business $business)
    {
        $this->authorize('delete', $business);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function maintenance(Business $business)
    {
        $this->authorize('update', $business);

        return view('business.maintenance', ['business' => $business]);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function balance(Business $business)
    {
        
        $this->authorize('update', $business);
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d');

        $balances = $bankAccountTitles = [];

        $result = [
            BankAccount::ACCOUNT_TYPE_REVENUE => [],
            BankAccount::ACCOUNT_TYPE_PRETOTAL => [],
            BankAccount::ACCOUNT_TYPE_SALESTAX => [],
            BankAccount::ACCOUNT_TYPE_PREREAL => [],
            BankAccount::ACCOUNT_TYPE_POSTREAL => []
        ];

        foreach ($business->accounts as $bankAccount) {
            $bankAccountTitles[$bankAccount->id] = $bankAccount->name;
            //TODO: check if we need this check
            if ($bankAccount->type != 'revenue') {
                $result[$bankAccount->type][$bankAccount->id][$today] = 0;
                if (count($bankAccount->allocations)) {
                    foreach ($bankAccount->allocations as $allocation) {
                        if ($allocation->allocatable_type == "App\Models\BankAccount") {
                            if ($allocation->allocation_date->format('Y-m-d') == $today) {
                                $result[$bankAccount->type][$bankAccount->id][$allocation->allocation_date->format('Y-m-d')] = $allocation->amount;
                            }
                        }
                    }
                }
            }
        }

        foreach ($result as $typeDataResult) {
            foreach ($typeDataResult as $bankAccountId => $allocationData) {
                if (array_key_exists($bankAccountId, $bankAccountTitles)) {
                    $balances[] = [
                        'title' => $bankAccountTitles[$bankAccountId],
                        'id' => $bankAccountId,
                        'amount' => $allocationData[$today] ?? 0.0
                    ];
                }
            }
        }

        $businesses = $this->getBusinessAll();
        $currentUser = Auth::user();
        $seatscount = getAvailable_seats($currentUser,$businesses);

        return view('business.balance', [
            'business' => $business,
            'balances' => $balances,
            'today' => $today,
            'countseats' => $seatscount,
        ]);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function balanceStore(Business $business, Request $request): \Illuminate\Http\RedirectResponse
    {
        $today = Timezone::convertToLocal(Carbon::now(), 'Y-m-d');
        $phaseId = $business->getPhaseIdByDate($today);

        //if more than 0 - we can laucnh recalculate
        $howManyChanges = 0;
//        dd($request->balance);
        foreach ($request->balance as $allocatable_id => $amount) {
            $allocation = Allocation::where(
                [
                    'allocatable_id' => $allocatable_id,
                    'allocatable_type' => 'App\Models\BankAccount',
                    'allocation_date' => $today
                ],
            )->first();

            if (
                !$allocation || (
                    $allocation
                    && number_format($allocation->amount, 0) != floatval($amount)
                )
            ) {
                //new record or update if amount not equal
                $allocation = Allocation::updateOrCreate(
                    [
                        'allocatable_id' => $allocatable_id,
                        'allocatable_type' => 'App\Models\BankAccount',
                        'allocation_date' => $today
                    ],
                    [
                        'phase_id' => $phaseId,
                        'amount' => $amount ?? 0,
                        'manual_entry' => 1
                    ]
                );
                $howManyChanges++;
            }
        }

        if ($howManyChanges > 0) {
            session()->flash('status', "Updated accounts: ".$howManyChanges);
        } else {
            session()->flash('status', "Nothing to update");
        }

        return redirect()->route('balance.business', ['business' => $business]);
    }
}
