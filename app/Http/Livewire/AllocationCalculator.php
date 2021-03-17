<?php

namespace App\Http\Livewire;

use App\Models\Business;
use Livewire\Component;

class AllocationCalculator extends Component
{

    public $selectOptions;
    public $selectedBusinessId;
    public Business $business;
    public $mappedAccounts;

    public function mount()
    {
        $this->selectOptions = $this->mapBusinessSelect();
        $this->business = auth()->user()->businesses->first();
        $this->selectedBusinessId = $this->business->id;
    }

    public function updatedSelectedBusinessId($new_value)
    {
        // $this->selectedBusinessId = $business_id;
        $this->business = Business::find($new_value);
        $refresh;

    }

    public function render()
    {
        $this->business = Business::find($this->selectedBusinessId) ?? auth()->user()->businesses->first();
        $this->mappedAccounts = $this->mapBusinessAccounts();
        return view('livewire.allocation-calculator');
    }

    public function mapBusinessSelect()
    {
        $businesses = auth()->user()->businesses;

        return $businesses->keyBy('id')->map(
            function ($business) {
                return $business->name;
            }
        );
    }

    public function mapBusinessAccounts()
    {
        return $this->business->accounts->mapToGroups(
            function($account)
            {
                return [$account->type => [
                    'id' => $account['id'],
                    'name' => $account['name']
                    ]
                ];
            }
        )->toArray();
    }

}
