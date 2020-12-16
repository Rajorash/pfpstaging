<?php

namespace App\Http\Controllers;

use App\BankAccountEntry;
use App\Business;
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
     * @param  \App\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccountEntry $bankAccountEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccountEntry  $bankAccountEntry
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
     * @param  \App\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business)
    {
        $this->authorize('view', $business);

        $validator = $this->validate($request, [
            'date'      => 'required|date',
            'amounts'   => 'array',
            'amounts.*' => 'numeric'
        ]);

        $date = $request->date;
        $amounts = collect($request->amount);

        $business_account_ids = $business->accounts->pluck('id');

        dump($amounts);
        // in order to pass business_accounts_ids to a closure you need to 'use ($var)'
        $amounts = $amounts->filter( function ($value, $key) use ($business_account_ids) {
            return in_array($key, $business_account_ids->values()->toArray());
        });

        foreach( $amounts as $account_id => $amount ) {
            // CHECK THAT THE ACCOUNTS BELONG TO THE BUSINESS
            // create or update account entry
        }

        dd( $date, $amounts, $business_account_ids );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccountEntry  $bankAccountEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccountEntry $bankAccountEntry)
    {
        //
    }
}
