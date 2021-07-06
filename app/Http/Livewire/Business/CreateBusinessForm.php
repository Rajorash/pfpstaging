<?php

namespace App\Http\Livewire\Business;

use Livewire\Component;

class CreateBusinessForm extends Component
{

    public $isOpen = false;

    public function openBusinessForm() {
        $this->isOpen = true;
    }

    public function render()
    {
        return view('business.create-business-form');
    }
}
