<?php

namespace App\Http\Controllers;

use App\TaxRate;
use App\Business;
use App\BankAccount;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        $salestaxAccounts = $business->accounts->where('type', '=', 'salestax');

        return view('taxrates.edit', compact('salestaxAccounts'));
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
        $data = $request->validate([
            'rate' => 'required|numeric',
            'account_id' => 'required'
        ]);

        $bank_account = BankAccount::find($data['account_id']);
        if (!$bank_account)
        {
            return response('Account not found', 404);
        }

        if (!$bank_account->taxRate)
        {
            $taxrate = new TaxRate();
            $taxrate->rate = $data['rate'];
            $taxrate->bank_account_id = $data['account_id'];
            $taxrate->save();

            // return response('Tax rate successfully created');
            return back();
        }

        $taxrate = $bank_account->taxRate;
        $taxrate->rate = $data['rate'];
        $taxrate->save();

        // return response('Tax rate successfully saved');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TaxRate  $taxRate
     * @return \Illuminate\Http\Response
     */
    public function show(TaxRate $taxRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TaxRate  $taxRate
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxRate $taxRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaxRate  $taxRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxRate $taxRate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TaxRate  $taxRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxRate $taxRate)
    {
        //
    }
}
