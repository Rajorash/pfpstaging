<?php

namespace App\Http\Livewire;

use App\Events\BusinessProcessed;
use App\Http\Controllers\UserController;
use App\Models\Advisor;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BusinessMaintenance extends Component
{
    public $email;
    public $email_collaborate;
    public $userId;
    public $advisorsClients = null;
    public $availableLicenses = 0;
    public $iWouldLikeToDelete = false;
    public $businessName = '';

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

        $this->businessName = $this->business->name;

        if ($this->business->license) {
            $this->activeLicense = $this->business->license->active;
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

        $collaborator = null;
        if ($this->email_collaborate) {
            $user = User::firstWhere('email', $this->email_collaborate);
            if ($user) {
                $collaborator = Advisor::firstWhere('user_id', $user->id);
            }
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

        if ($this->businessName != $this->business->name) {
            $this->business->name = $this->businessName;
            $this->business->save();
        }

        if ($newOwner && (!$this->business->owner_id || $newOwner->id != $this->business->owner_id)) {
            $this->business->owner()->associate($newOwner);
            $this->business->save();
            event(new BusinessProcessed('newOwner', $this->business, Auth::user()));
        }

        if ($collaborator) {
            $this->business->collaboration()->associate($collaborator);
            $this->business->save();
            event(new BusinessProcessed('collaboration', $this->business, Auth::user()));
        }

        if ($this->iWouldLikeToDelete) {
            event(new BusinessProcessed('delete', $this->business, Auth::user()));
            $this->business->delete();
        }

        return redirect("business");
    }

    public function render()
    {
        return view('business.maintenance-livewire');
    }
}
