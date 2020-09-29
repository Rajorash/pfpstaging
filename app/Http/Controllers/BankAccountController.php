<?php

namespace App\Http\Controllers;

use Auth;
use App\AccountFlow;
use App\BankAccount;
use App\Business;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        $this->authorize('view', $business);
        
        $accounts = $business->accounts;
        // $accounts = [];
        return view('accounts.show', ['accounts' => $accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Business $business)
    {
        $this->authorize('createBankAccount', $business);

        $accounts = $business->accounts;
        
        return view('accounts.create', ['business' => $business]);
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFlow(BankAccount $account)
    {
        $this->authorize('createBankAccount', $account->business);

        return view('accounts.createflow', ['account' => $account]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Business $business)
    {
        $data = $request->validate([
            'name' => 'required',
            'account_type' => 'required'
        ]);

        $account = new BankAccount();
        $account->name = $data['name'];
        $account->type = $data['account_type'];
        $account->business_id = $business->id;

        $account->save();

        return redirect("business/".$business->id."/accounts");
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFlow(Request $request, BankAccount $account)
    {
        $this->authorize('createBankAccount', $account->business);

        $data = $request->validate([
            'label' => 'required',
            'flow-direction' => 'required'
        ]);

        $flow = new AccountFlow();
        $flow->label = $data['label'];
        $flow->negative_flow = $data['flow-direction'];
        $flow->account_id = $account->id;
        
        $flow->save();
        
        return redirect("business/".$account->business->id."/accounts");
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business, BankAccount $account)
    {
        $this->authorize('update', $account);        

        return view('accounts.edit', ['business' => $business, 'account' => $account, 'curr_type' => $account->type ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business, BankAccount $account)
    {
        // authorise
        
        $data = request()->validate([
            'name' => 'required',
            'account_type' => 'required']
        );

        $account->name = $data['name'];
        $account->type = $data['account_type'];
        $account->save();

        return redirect("business/".$business->id."/accounts");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business, BankAccount $account)
    {

        // authorise first

        $account->delete();

        return redirect("/business/{$business->id}/accounts");
    }
}
