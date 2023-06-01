<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Models\Business;
use App\Models\License;
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
        $filtered = $this->getUserList();

        abort_if($filtered == null, 403, 'Access denied');

        return view('user.list',
            [
                'users' => $filtered,
                'currUserId' => $currUserId
            ]
        );
    }

    private function getUserList()
    {
        if (Auth::user()->isSuperAdmin()) {
            return User::orderBy('name')->paginate($this->perPage);
        }

        if (Auth::user()->isRegionalAdmin()) {
            $licenses = License::whereRegionaladminId(Auth::user()->id);

            $collection = $licenses->pluck('advisor_id');
            if (Auth::user()->advisorsByRegionalAdmin) {
                $collection = $collection->merge(Auth::user()->advisorsByRegionalAdmin->pluck('id'));
            }
            $collection = $collection->unique();

            return User::whereIn('id', $collection)
                ->withCount('businesses', 'roles')
                ->orderBy('name')
                ->paginate($this->perPage);

        }

        if (Auth::user()->isAdvisor()) {
            return User::where(
                function ($subQuery) {
                    $subQuery->whereIn('id', Auth::user()->licenses->pluck('owner_id'));
                    $subQuery->OrwhereIn('id', Auth::user()->clientsByAdvisor->pluck('id'));
                })->with('businesses', 'roles')
                ->orderBy('name')
                ->paginate($this->perPage);
            //$filtered = User::whereIn('id', Auth::user()->advisors->pluck('id'));
        }

        return null;
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

        return view(
            'user.edit',
            [
                'user' => $user,
            ]
        );
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
