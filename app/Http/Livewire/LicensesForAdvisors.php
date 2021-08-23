<?php

namespace App\Http\Livewire;

use App\Events\LicenseForAdvisorChanged;
use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LicensesForAdvisors extends Component
{
    use WithPagination;

    public User $user;
    public $licensesCounter;
    public $licensesCounterMessage;
    public $allowEdit = false;

    protected $perPage = 10;
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $licensesCounterHistory = $this->user->advisorsLicenses;
        if ($licensesCounterHistory && $licensesCounterHistory->last()) {
            $this->licensesCounter = $licensesCounterHistory->last()->licenses;
        }
    }

    public function updatedAllowEdit()
    {
        $this->licensesCounterMessage = '';
    }

    public function updatedLicensesCounter()
    {
        if ($this->allowEdit) {
            $this->licensesCounter = abs(intval($this->licensesCounter));
            $LicensesForAdvisors = new \App\Models\LicensesForAdvisors();
            $LicensesForAdvisors->licenses = $this->licensesCounter;
            $LicensesForAdvisors->advisor()->associate($this->user);
            $LicensesForAdvisors->regionalAdmin()->associate(Auth::user());
            $LicensesForAdvisors->save();

            event(new LicenseForAdvisorChanged($this->user, Auth::user(), $this->licensesCounter));

            $this->licensesCounterMessage = 'Count of licenses updated successfully';
            $this->allowEdit = false;
        }
    }

    public function getAdvisorsLicensesAttribute()
    {
        return License::whereAdvisorId($user->id)->get();
    }

    public function render()
    {
        $licensesCurrent = License::whereAdvisorId($this->user->id)
            // ->with('regionalAdmin')
            ->orderByDesc('created_at')
            ->get();

        $licensesCounterHistory = \App\Models\LicensesForAdvisors::where('advisor_id', '=', $this->user->id)
            ->with('regionalAdmin')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('license.advisor-details',
            [
                'licensesCurrent' => $licensesCurrent,
                'licensesCounterHistory' => $licensesCounterHistory
            ]
        );
    }
}
