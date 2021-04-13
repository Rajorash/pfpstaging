<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use GettersTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        if (Auth::user()->isSuperAdmin()) {
            $filtered = $users;
        } else {
            $filtered = $users->filter(function ($user) {
                return Auth::user()->can('view', $user);
            })->values();
            // return $filtered;
        }

        return view('user.list', ['users' => $filtered]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $this->authorize('create', $user);

        $ownerRoleId = auth()->user()->roles->min('id');
        $roles = Role::where('id', '>', $ownerRoleId)->get()->pluck('label', 'id')->toArray();

        return view('user.create', ['roles' => $roles]);
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
            'roles' => 'required',
        ]);

        // create and add user
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make(Str::random(10));
        $user->timezone = $data['timezone'];
        $user->save();

        $ownerRoleId = auth()->user()->roles->min('id');
        // assign client role
        foreach ($data['roles'] as $role_id)
        {
            if ($ownerRoleId < $role_id) {
                $client_role = Role::find($role_id);
                $user->assignRole($client_role);
            }
        }

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
        $this->authorize('edit', $user);

        $ownerRoleId = auth()->user()->roles->min('id');
        $roles = Role::where('id', '>', $ownerRoleId)->get()->pluck('label', 'id')->toArray();
        $userRoles = $user->roles->pluck('id')->toArray();

        $businesses = $licenses = [];
        if (in_array(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles)) {

            $businesses = $this->getBusinessAll();
            if (!Auth::user()->isSuperAdmin()) {
                $businesses = $businesses->filter(function ($business) {
                    return Auth::user()->can('view', $business);
                })->values();
            }
            $businesses = $businesses->pluck('name', 'id')->toArray();
            $licenses = $user->licenses->pluck('id')->toArray();
        }

        return view(
            'user.edit',
            [
                'user' => $user,
                'userRoles' => $userRoles,
                'roles' => $roles,
                'businesses' => $businesses,
                'licenses' => $licenses
            ]
        );
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
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'timezone' => 'present|timezone',
            'roles' => 'required',
        ]);
        $validator->validate();

        $advisorId = User::ROLE_IDS[User::ROLE_ADVISOR];
        $validator->after(function ($validator) use ($request, $advisorId) {
            if (
                is_array($request->licenses) &&
                is_array($request->roles) &&
                !in_array($advisorId, $request->roles)
            ) {
                $validator->errors()->add(
                    'roles', 'Advisor role can not be revoked if at least one business is selected for licensing.'
                );
            }
        });

        if ($validator->fails()) {
            $userRoles = $user->roles->pluck('id')->toArray();
            $ownerRoleId = auth()->user()->roles->min('id');
            $roles = Role::where('id', '>', $ownerRoleId)->get()->pluck('label', 'id')->toArray();
            $businesses = $licenses = [];
            if (in_array(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles)) {

                $businesses = $this->getBusinessAll();
                if (!Auth::user()->isSuperAdmin()) {
                    $businesses = $businesses->filter(function ($business) {
                        return Auth::user()->can('view', $business);
                    })->values();
                }
                $businesses = $businesses->pluck('name', 'id')->toArray();
                $licenses = $user->licenses->pluck('id')->toArray();
            }

            return view(
                'user.edit',
                [
                    'user' => $user,
                    'userRoles' => $userRoles,
                    'roles' => $roles,
                    'businesses' => $businesses,
                    'licenses' => $licenses,
                    "errors"  => $validator->messages()
                ]
            );
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->timezone = $request->timezone;
        $user->save();

        $user->licenses()->detach();
        if (is_array($request->licenses)) {

            $time_created = date('Y-m-d h:i:s', time());
            foreach ($request->licenses as $license)
            {
                $business = Business::find($license);
                $user->assignLicense([
                    $business->id => [
                        'account_number' => uniqid(),
                        'created_at' => $time_created,
                        'updated_at' => $time_created
                    ]
                ]);
            }
        }

        $ownerRoleId = auth()->user()->roles->min('id');
        $userRoles = $user->roles->pluck('id')->toArray();
        $toDetach = [];
        foreach ($userRoles as $role_id) {
            if ($ownerRoleId > $role_id && ($role_id != User::ROLE_ADVISOR || empty($request->licenses))) {
                $toDetach[] = $role_id;
            }
        }
        $user->roles()->detach($toDetach);
        foreach ($request->roles as $role_id)
        {
            if ($ownerRoleId < $role_id) {
                $client_role = Role::find($role_id);
                $user->assignRole($client_role);
            }
        }

        return redirect("user");
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
