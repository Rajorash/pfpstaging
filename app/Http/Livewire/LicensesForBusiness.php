<?php

namespace App\Http\Livewire;

use App\Events\BusinessProcessed;
use App\Http\Controllers\UserController;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LicensesForBusiness extends Component
{
    public $email;
    public $userId;
    public $advisorsClients = null;
    public $availableLicenses = 0;
    public $allowToSetLicense = false;
    public $expired;

    public $failure = false;
    public $failureMessage;

    public $business;
    public $activeLicense = false;

    private $UserController;

    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->advisorsClients = User::whereIn('id', Auth::user()->clientsByAdvisor->pluck('id'))
            ->orderBy('name')
            ->get();

        $this->UserController = new UserController();
    }

    public function mount()
    {
        $this->freshData();

        if ($this->business->license) {
            $this->activeLicense = $this->business->license->active;
            $this->allowToSetLicense = true;
        } else {
            if ($this->availableLicenses > 0) {
                $this->allowToSetLicense = true;
            }
        }

        if ($this->business->owner_id) {
            $this->userId = $this->business->owner_id;
        }
    }

    protected function freshData()
    {
        $this->availableLicenses = Auth::user()->seats - count(Auth::user()->activeLicenses);

        $this->business = $this->business->fresh();
    }

    public function store()
    {
        $this->failure = false;
        $this->failureMessage = '';

        if (!$this->userId && !$this->email) {
            $this->failure = true;
            $this->failureMessage = 'Client must be set';
        }

        $newOwner = null;
        if (!$this->userId && $this->email) {
            $newOwner = User::firstWhere('email', $this->email);
        } elseif ($this->userId && !$this->email) {
            $newOwner = User::firstWhere('id', $this->userId);
        }

        if (!$newOwner) {
            $this->failure = true;
            $this->failureMessage = 'Client not found';
        } else {
            if (!$newOwner->isClient()) {
                $this->failure = true;
                $this->failureMessage = 'Only clients can be added to Business.';
            } else {
                $this->userId = $newOwner->id;
            }
        }

        if ($newOwner && !$this->business->owner_id) {
            $this->business->owner()->associate($newOwner);
            $this->business->save();
            event(new BusinessProcessed('newOwner', $this->business));
        }

        //business without licenses - create it and enable
        if (!$this->business->license) {
            if ($this->activeLicense && $this->availableLicenses) {
                $license = License::create([
                    'active' => true,
                    'account_number' => uniqid(),
                    'business_id' => $this->business->id,
                    'advisor_id' => Auth::user()->id,
                ]);
                $this->business->license()->save($license);
                $this->freshData();
            }
        }

        //disable current license
        if ($this->business->license && !$this->activeLicense) {
            $this->business->license()->update(['active' => false]);
            $this->freshData();
        }
        //enable old license
        if ($this->business->license
            && !$this->business->license->active
            && $this->activeLicense) {
            $this->business->license()->update(['active' => true]);
            $this->freshData();
        }

        return redirect("business");
    }

    public function render()
    {
        return view('license.licenses-for-business');
    }
}
