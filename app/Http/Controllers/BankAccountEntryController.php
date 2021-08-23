<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankAccountEntry;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BankAccountEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccountEntry $bankAccountEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business)
    {
        $this->authorize('view', $business);

        $accounts = $business->accounts->load('flows');

        return view('account-entry.edit', ['accounts' => $accounts, 'business' => $business]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business)
    {
        $this->authorize('view', $business);

        $validator = $this->validate($request, [
            // 'date'      => 'required|date',
            'amounts'   => 'array',
            'amounts.*' => 'numeric'
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $amounts = collect($request->amounts);

        // get all account ids belonging to the business
        $business_account_ids = $business->accounts->pluck('id');

        // filter out any entries that do not belong to the business entered
        // in order to pass business_accounts_ids to a closure you need to 'use ($var)'
        $amounts = $amounts->filter( function ($value, $account_id) use ($business_account_ids) {
            return in_array($account_id, $business_account_ids->values()->toArray());
        });

        $phase_id = $business->getPhaseIdByDate(Carbon::tomorrow());

        foreach( $amounts as $account_id => $amount ) {
            // find or create an entry
            if($account = BankAccount::find($account_id)) {
                $account->allocate($amount, $today, $phase_id, true);
            }
        }

        return redirect()->back()->with('success', 'Account entries successfully entered.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccountEntry $bankAccountEntry)
    {
        //
    }
}
