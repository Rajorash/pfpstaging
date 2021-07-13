<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Traits\GettersTrait;
use Livewire\WithPagination;

//use Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use GettersTrait;
    use WithPagination;

    protected $perPage = 10;
    protected $paginationTheme = 'tailwind';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('indexUsers', Auth::user());

        $currUserId = auth()->user()->id;
        $filtered = null;

        if (Auth::user()->isSuperAdmin()) {
            $filtered = User::orderBy('name')->paginate($this->perPage);
        } elseif (Auth::user()->isRegionalAdmin()) {
            if (Auth::user()->advisors) {
                $filtered = User::whereIn('id', Auth::user()->advisors->pluck('id'))
                    ->with('businesses')
                    ->orderBy('name')
                    ->paginate($this->perPage);
            }
        } elseif (Auth::user()->isAdvisor()) {
            //TODO - refactored to Advisors can see clients they manage and businesses they have licensed (past or present)
            $filtered = User::orderBy('name')->paginate($this->perPage);
        } else {
            abort(403, 'Access denied');
        }

        return view('user.list',
            [
                'users' => $filtered,
                'currUserId' => $currUserId
            ]
        );
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

//        $ownerRoleId = auth()->user()->roles->min('id');
//        $roles = $this->getRolesAllowedToGrant();

        return view('user.create'
//            , ['roles' => $roles]
        );
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
        $user->active = boolval($request['active']);
        $user->title = $request['title'];
        $user->responsibility = $request['responsibility'];
        $user->save();

        $ownerRoleId = auth()->user()->roles->min('id');
        // assign client role
        foreach ($data['roles'] as $role_id) {
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

//        $ownerRoleId = auth()->user()->roles->min('id');
//        $roles = $this->getRolesAllowedToGrant();
//        $userRoles = $user->roles->pluck('id')->toArray();
//        $userRoleLabels = $user->roles->pluck('label')->toArray();
//
//        $businesses = $licenses = [];
//        if (in_array(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles)) {
//
//            $businesses = $this->getBusinessAll();
//            if (!Auth::user()->isSuperAdmin()) {
//                $businesses = $businesses->filter(function ($business) {
//                    return Auth::user()->can('view', $business);
//                })->values();
//            }
//            $businesses = $businesses->pluck('name', 'id')->toArray();
//            $licenses = $user->licenses->pluck('id')->toArray();
//        }

        return view(
            'user.edit',
            [
                'user' => $user,
//                'userRoles' => $userRoles,
//                'userRoleLabels' => $userRoleLabels,
//                'roles' => $roles,
//                'businesses' => $businesses,
//                'licenses' => $licenses
            ]
        );
    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \App\Models\User  $user
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, User $user)
//    {
//        $validator = \Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email|unique:users,email,'.$user->id,
//            'timezone' => 'present|timezone',
//            'roles' => 'required',
//        ]);
//        $validator->validate();
//
//        $advisorId = User::ROLE_IDS[User::ROLE_ADVISOR];
//        $validator->after(function ($validator) use ($request, $advisorId) {
//            if (
//                is_array($request->licenses)
//                && is_array($request->roles)
//                && !in_array($advisorId, $request->roles)
//            ) {
//                $validator->errors()->add(
//                    'roles', 'Advisor role can not be revoked if at least one business is selected for licensing.'
//                );
//            }
//        });
//
//        if ($validator->fails()) {
//            $userRoles = $user->roles->pluck('id')->toArray();
//            $ownerRoleId = auth()->user()->roles->min('id');
//            $roles = $this->getRolesAllowedToGrant();
//            $businesses = $licenses = [];
//            if (in_array(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles)) {
//
//                $businesses = $this->getBusinessAll();
//                if (!Auth::user()->isSuperAdmin()) {
//                    $businesses = $businesses->filter(function ($business) {
//                        return Auth::user()->can('view', $business);
//                    })->values();
//                }
//                $businesses = $businesses->pluck('name', 'id')->toArray();
//                $licenses = $user->licenses->pluck('id')->toArray();
//            }
//
//            return view(
//                'user.edit',
//                [
//                    'user' => $user,
//                    'userRoles' => $userRoles,
//                    'roles' => $roles,
//                    'businesses' => $businesses,
//                    'licenses' => $licenses,
//                    'errors' => $validator->messages()
//                ]
//            );
//        }
//
//        $user->name = $request->name;
//        $user->email = $request->email;
//        $user->timezone = $request->timezone;
//        $user->active = boolval($request->active);
//        $user->title = $request['title'];
//        $user->responsibility = $request['responsibility'];
//        $user->save();
//
//        $user->licenses()->detach();
//        if (is_array($request->licenses)) {
//
//            $time_created = date('Y-m-d h:i:s', time());
//            foreach ($request->licenses as $license) {
//                $business = Business::find($license);
//                $user->assignLicense([
//                    $business->id => [
//                        'account_number' => uniqid(),
//                        'created_at' => $time_created,
//                        'updated_at' => $time_created
//                    ]
//                ]);
//            }
//        }
//
//        $ownerRoleId = auth()->user()->roles->min('id');
//        $userRoles = $user->roles->pluck('id')->toArray();
//        $toDetach = [];
//        foreach ($userRoles as $role_id) {
//            if (!in_array($role_id, $request->roles) && ($role_id != User::ROLE_ADVISOR || empty($request->licenses))) {
//                $toDetach[] = $role_id;
//            }
//        }
//
//        $user->roles()->detach($toDetach);
//        foreach ($request->roles as $role_id) {
//            if ($ownerRoleId < $role_id) {
//                $client_role = Role::find($role_id);
//                $user->assignRole($client_role);
//            }
//        }
//
//        return redirect("user");
//    }

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

    /**
     * Get array of Roles current user is allowed to grant to others
     * @return array
     */
    public function getRolesAllowedToGrant()
    {
        $roles = Role::all()->pluck('label', 'id')->toArray();
        if (Auth::user()->isSuperAdmin()) {
            unset($roles[1]);
            return $roles;
        }
        $user = auth()->user();
        return $user->roles->map(function ($item) use ($roles) {
            if ($item->id < 4) {
                $item['allowed_id'] = ($item->id + 1);
                $item['allowed_label'] = $roles[($item->id + 1)];
            }
            return $item;
        })->pluck('allowed_label', 'allowed_id')->filter(function ($value, $key) {
            return !is_null($value);
        })->toArray();
    }

    /**
     * Check if current user has at least one license to create/activate Client
     * @return bool
     */
    private function hasActiveLicense()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('id')->toArray();
        if (
            Auth::user()->isSuperAdmin() ||
            in_array(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles)
        ) {
            ;
        }

        return false;
    }

    /**
     * Check if $userRoles contains Advisor role
     * @param  array  $userRoles
     * @return bool
     */
    public function checkAdvisor(array $userRoles)
    {
        return key_exists(User::ROLE_IDS[User::ROLE_ADVISOR], $userRoles);
    }

    /**
     * Check if $userRoles contains Client role
     * @param  array  $userRoles
     * @return bool
     */
    public function checkClient(array $userRoles)
    {
        return key_exists(User::ROLE_IDS[User::ROLE_CLIENT], $userRoles);
    }

    protected function getUsersByType($type)
    {
        $users = User::where('id', '!=', '0')
            ->with('roles')
            ->orderBy('name')
            ->get();

        switch ($type) {
            case User::ROLE_SUPERADMIN:
                $users = $users->filter->isSuperAdmin();
                break;
            case User::ROLE_ADMIN:
                $users = $users->filter->isRegionalAdmin();
                break;
            case User::ROLE_ADVISOR:
                $users = $users->filter->isAdvisor();
                break;
            case User::ROLE_CLIENT:
                $users = $users->filter->isClient();
                break;
        }

        return $users;
    }

    public function getSuperAdmins()
    {
        return $this->getUsersByType(User::ROLE_SUPERADMIN);
    }

    public function getAdmins()
    {
        return $this->getUsersByType(User::ROLE_ADMIN);
    }

    public function getAdvisor()
    {
        return $this->getUsersByType(User::ROLE_ADVISOR);
    }

    public function getClient()
    {
        return $this->getUsersByType(User::ROLE_CLIENT);
    }
}
