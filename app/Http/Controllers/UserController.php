<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        $filtered = $users->filter(function ($user) {
            return Auth::user()->can('view', $user);
        })->values();
        // return $filtered;

        return view('user.list', ['users' => $filtered]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
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
            'name' => 'required',
            'business_name' => 'required',
            'email' => 'unique:users,email|required|email',
            'timezone' => 'present|timezone',
        ]);

        // create and add user
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make(Str::random(10));
        $user->timezone = $data['timezone'];
        $user->save();

        // assign client role
        $client_role = \App\Models\Role::where('name', 'client')->first();
        $user->assignRole($client_role);

        // add business
        $business = new \App\Models\Business;
        $business->name = $data['business_name'];
        $business->owner_id = $user->id;
        $business->save();

        // grant license to business
        $advisor = Auth::user();
        $license = new \App\Models\License;
        $license->account_number = uniqid();
        $license->business_id = $business->id;
        $license->advisor_id = $advisor->id;
        $license->save();

        // redirect to user view of newly created user
        return redirect("user/{$user->id}");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('user.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
