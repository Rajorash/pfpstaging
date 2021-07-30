<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\AccountFlow;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        $accounts = $business->accounts->load('flows');

        return view('accounts.show', ['accounts' => $accounts, 'business' => $business]);
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

        return view('accounts.createflow', ['account' => $account, 'business' => $account->business]);
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

        $phases = Phase::where('business_id', '=', $business->id)->get()->pluck('id');
        if (count($phases)) {
            foreach ($phases as $phase) {
                $key = 'phasePercentValues_'.$phase.'_'.$business->id;
                Cache::forget($key);
            }
        }

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
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $account
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
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business, BankAccount $account)
    {
        // authorise

        $data = request()->validate([
            'name' => 'required',
            'type' => 'required']
        );

        $account->name = $data['name'];
        $account->type = $data['type'];
        $account->save();

        return redirect("business/".$business->id."/accounts");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function editFlow(BankAccount $account, AccountFlow $flow)
    {
        $this->authorize('update', $account);

        return view('accounts.editflow', ['flow' => $flow, 'account' => $account, 'curr_type' => $account->type, 'business' => $account->business ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function updateFlow(Request $request, BankAccount $account, AccountFlow $flow)
    {
        $this->authorize('update', $account);

        $data = request()->validate([
            'label' => 'required',
            'flow-direction' => 'required']
        );

        $flow->label = $data['label'];
        $flow->negative_flow = $data['flow-direction'];
        $flow->save();

        return redirect("business/".$account->business->id."/accounts");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business, BankAccount $account)
    {

        dd($business,$account);
        $this->authorize('view', $business);

        $account->delete();

        return redirect("/business/{$business->id}/accounts");
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $account
     * @return \Illuminate\Http\Response
     */
    public function destroyFlow(BankAccount $account, AccountFlow $flow)
    {
        $this->authorize('view', $account->business);

        $flow->delete();

        return redirect("/business/{$account->business->id}/accounts");
    }
}
