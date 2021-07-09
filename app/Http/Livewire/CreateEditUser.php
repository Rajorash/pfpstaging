<?php

namespace App\Http\Livewire;

use App\Http\Controllers\UserController;
use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;

class CreateEditUser extends Component
{
    // User fields
    public $name;
    public $email;
    public $title;
    public $responsibility;
    public $roles = [];
    public $rolesArray = [];
    public $timezone;

    //User obj for Edit page
    public $user;
    //array for Businesses
    public $businesses = [];
    //array for Licenses
    public $licenses = [];

    public $adminsUsersArray = [];
    public $selectedAdminId;
    public $selectedAdminIdAllowEdit = false;

    //Int ID for Advisor Role. Uses for check during edit role and current existing licenses
    public $roleAdvisorId;

    /**
     * @var App\Http\Controllers\UserController
     */
    private $UserController;

    /**
     * CreateEditUser constructor.
     * @param  null  $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->UserController = new UserController();
        $this->roleAdvisorId = User::ROLE_IDS[User::ROLE_ADVISOR];

        if (Auth::user()->isSuperAdmin()) {
            $this->adminsUsersArray = $this->UserController->getAdmins();
        }
    }

    /**
     * Function fires on initial load and check
     * prerequisites for creating or editing a User
     *
     * @return void
     */
    public function mount()
    {
        $this->rolesArray = $this->UserController->getRolesAllowedToGrant();

        if ($this->user) {
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->timezone = $this->user->timezone;
            $this->roles = $this->user->roles->pluck('id', 'id')->toArray();
            $this->title = $this->user->title;
            $this->responsibility = $this->user->responsibility;

            $this->businesses = $this->licenses = [];

            if ($this->UserController->checkAdvisor($this->roles)) {
                $this->getBusinessAndLicensesForAdvisor();
            }

            if ($this->user->isAdvisor() && Auth::user()->isSuperAdmin()) {
                $this->selectedAdminId = isset($this->user->regionalAdmin[0]) ? $this->user->regionalAdmin[0]->id : null;
                $this->selectedAdminIdAllowEdit = false;
            }
        }
    }

    /**
     * check if User is Advisor, and check current licenses
     */
    private function getBusinessAndLicensesForAdvisor()
    {
        $this->businesses = $this->UserController->getBusinessAll();
        if (!Auth::user()->isSuperAdmin()) {
            $this->businesses = $this->businesses->filter(function ($business) {
                return Auth::user()->can('view', $business);
            })->values();
        }

        $this->businesses = $this->businesses->pluck('name', 'id')->toArray();

        //new user
        if (!$this->user) {
            if (!$this->UserController->checkAdvisor($this->roles)) {
                $this->businesses = [];
            }
        }

        //edit user
        if ($this->user) {
            $this->licenses = empty($this->licenses)
                ? $this->user->licenses->pluck('id', 'id')->toArray()
                : $this->licenses;
            if (!$this->UserController->checkAdvisor($this->roles)) {
                $this->licenses = [];
                $this->businesses = [];
            }
        }
    }

    private function resetInputFields()
    {
        $this->name = null;
        $this->email = null;
        $this->timezone = null;
        $this->roles = [];
        $this->licenses = [];
        $this->businesses = [];
        $this->title = null;
        $this->responsibility = null;
    }

    /**
     * hook where Roles updated on front
     */
    public function updatedRoles()
    {
        $this->roles = array_filter($this->roles);
        $this->getBusinessAndLicensesForAdvisor();
    }

    /**
     * hook where Licenses updated on front
     */
    public function updatedLicenses()
    {
        $this->licenses = array_filter($this->licenses);
    }

    /**
     * Save new or update existing User with licensing and roles
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        $validator = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'timezone' => $this->timezone,
            'roles' => $this->roles,
            'selectedAdminId' => $this->selectedAdminId
        ], [
            'name' => 'required|min:6',
            'email' => 'required|email|unique:users,email'.($this->user ? ','.$this->user->id : ''),
            'timezone' => 'present|timezone',
            'roles' => 'required',
            'selectedAdminId' => (auth()->user()->isSuperAdmin()
                && in_array($this->roleAdvisorId, $this->roles))
                ? 'required'
                : ''
        ]);

        $validator->validate();

        $validator->after(function ($validator) {
            if (
                !empty($this->licenses)
                && !empty($this->roles)
                && !$this->UserController->checkAdvisor($this->roles)
            ) {
                $validator->errors()->add(
                    'roles', 'Advisor role can not be revoked if at least one business is selected for licensing.'
                );
            }
        });

        if (!$validator->fails()) {
            // create and add user
            $user = $this->user ? $this->user : new User;
            $user->name = $this->name;
            $user->email = $this->email;
            $user->timezone = $this->timezone;
            $user->title = $this->title;
            $user->responsibility = $this->responsibility;

            $user->active = 1; //only for old Login code
            if (!$this->user) {
                //password only for new user
                $user->password = Hash::make(Str::random(10));
            }

            $user->save();

            //reattach licenses
            $user->licenses()->detach();
            if (is_array($this->licenses)) {

                $time_created = date('Y-m-d h:i:s', time());
                foreach ($this->licenses as $license) {
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

            //detach removed roles
            if ($this->user) {
                $userRoles = $this->user->roles->pluck('id')->toArray();
                $toDetach = [];
                foreach ($userRoles as $role_id) {
                    if (!in_array($role_id,
                            $this->roles) && ($role_id != User::ROLE_ADVISOR || empty($this->licenses))) {
                        $toDetach[] = $role_id;
                    }
                }
                $user->roles()->detach($toDetach);
            }

            // assign client role
            $ownerRoleId = auth()->user()->roles->min('id');
            foreach ($this->roles as $role_id) {
                if ($ownerRoleId < $role_id) {
                    $client_role = Role::find($role_id);
                    $user->assignRole($client_role);

                    if ($role_id == User::ROLE_IDS[User::ROLE_ADVISOR]) {
                        if (auth()->user()->isSuperAdmin() && $this->selectedAdminIdAllowEdit) {
                            $user->regionalAdmin()->sync(User::find($this->selectedAdminId));
                        } elseif (auth()->user()->isRegionalAdmin()) {
                            $user->regionalAdmin()->sync(auth()->user());
                        }
                    }
                }
            }

            if ($this->user) {
                return redirect("user");
            } else {
                return redirect("user/{$user->id}");
            }
        }
    }

    public function render()
    {
        return view('user.create-edit-user');
    }
}
