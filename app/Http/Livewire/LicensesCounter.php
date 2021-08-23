<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LicensesCounter extends Component
{
    public $initial;
    public $user;

    public function mount()
    {
        $this->initial = LicensesForAdvisors::DEFAULT_LICENSES_COUNT;
    }
    public function render()
    {
        return view('livewire.licenses-counter');
    }

    public function licincrement()
    {
        $this->initial+=1;
    }

    public function licdecrement()
    {
        if($this->initial>0){
            $this->initial-=1;
        }else{
            session()->flash('info', "You cannot have negative value in the counter");
        }
    }
}
