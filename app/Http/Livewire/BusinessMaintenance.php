<?php

namespace App\Http\Livewire;

use App\Events\BusinessProcessed;
use App\Http\Controllers\UserController;
use App\Models\Advisor;
use App\Models\Collaboration;
use App\Models\License;
use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
// use Excel;
// use App\Exports\ExpenseExport;

class BusinessMaintenance extends Component
{
    public string $email = '';
    public string $emailCollaborate = '';
    public string $userId = '';
    public $advisorsClients = null;
    public $availableLicenses = 0;
    public $iWouldLikeToDelete = false;
    public $checkTotalProjection = false;
    public string $businessName = '';
    public $iWouldLikeToChangeStartDate = false;
    public $businessStartDate;

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
            ->with('roles')
            ->get();

        $this->UserController = new UserController();
    }

    public function mount()
    {
        $this->freshData();

        $this->businessName = $this->business->name;
        $this->businessStartDate = $this->business->start_date;

        $business_flag = Business::find($this->business->id);

        if($business_flag->flag){
            $this->checkTotalProjection = true;
        }else{
            $this->checkTotalProjection = false;
        }

        if ($this->business->license) {
            $this->activeLicense = $this->business->license->active;
        }

        if ($this->business->owner_id) {
            $this->userId = $this->business->owner_id;
        }

        if ($this->business->collaboration) {
            $this->emailCollaborate = $this->business->collaboration->advisor->user->email;
        }

        session(['currentid' => $this->business->id]);

        if ((is_object($this->business->collaboration)
                && $this->business->collaboration->advisor->user_id != auth()->user()->id)
            || !is_object($this->business->collaboration)) {
            //allowed
        } else {
            abort_if(true, 403, 'Access denied');
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

        $validator = Validator::make([
            'businessName' => $this->businessName,
            'emailCollaborate' => $this->emailCollaborate,
            'businessStartDate' => $this->businessStartDate
        ], [
            'businessName' => 'required',
            'emailCollaborate' => 'nullable|email',
            'businessStartDate' => 'nullable|date'
        ]);

        $validator->validate();

        $validator->after(function ($validator) {
            if ($this->emailCollaborate) {
                $user = User::firstWhere('email', $this->emailCollaborate);
                if (!$user) {
                    $validator->errors()->add(
                        'emailCollaborate', 'Advisor not found, set correct Advisor\'s email'
                    );
                    $this->failure = true;
                    $this->failureMessage = 'Advisor not found, set correct Advisor\'s email';
                } elseif ($user && !$user->isAdvisor()) {
                    $validator->errors()->add(
                        'emailCollaborate', 'This User is not Advisor'
                    );
                    $this->failure = true;
                    $this->failureMessage = 'This User is not Advisor';
                }
            }
        });

        if (!$validator->fails()) {
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
            if ($this->emailCollaborate) {
                $user = User::firstWhere('email', $this->emailCollaborate);
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

            if ($this->iWouldLikeToChangeStartDate) {
                $this->business->start_date = $this->businessStartDate;
                $this->business->save();
            }

            if ($collaborator
                && $this->emailCollaborate != optional(optional(optional($this->business->collaboration)->advisor)->user)->email) {

                Collaboration::where('business_id', '=', $this->business->id)->delete();
                $time_created = date('Y-m-d h:i:s', time());
                Collaboration::create([
                    'advisor_id' => $collaborator->id,
                    'business_id' => $this->business->id,
                    'created_at' => $time_created,
                    'updated_at' => $time_created
                ]);
                $this->freshData();
                event(new BusinessProcessed('collaboration', $this->business, Auth::user()));
            } elseif (empty($this->emailCollaborate) && is_object($this->business->collaboration)) {
                event(new BusinessProcessed('collaborationDelete', $this->business, Auth::user()));
                Collaboration::where('business_id', '=', $this->business->id)->delete();
                event(new BusinessProcessed('collaborationRevoke', $this->business, Auth::user()));
            }
       
                // session(['business_id_'.$this->business->id => $this->business->id]);
            
                $business_flag = Business::find($this->business->id);

                if($this->checkTotalProjection){
                        if($business_flag) {
                            $business_flag->flag = 1;
                            $business_flag->save();
                        }
                }else{
                        if($business_flag) {
                            $business_flag->flag = 0;
                            $business_flag->save();
                        }
                }


       
            $checkWouldLikeToDelete = session('iWouldLikeToDelete') ? session('iWouldLikeToDelete') : $this->iWouldLikeToDelete ;

            if(isset($checkWouldLikeToDelete))
            {
                $this->iWouldLikeToDelete = $checkWouldLikeToDelete;
            }
            
            if ($this->iWouldLikeToDelete == 1) {
                event(new BusinessProcessed('delete', $this->business, Auth::user()));
                $this->business->delete();
                session()->forget('iWouldLikeToDelete');
            }

            
            return redirect("business");
        }
    }

    public function render()
    {
        return view('business.maintenance-livewire');
    }
}
