<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Business;
use Livewire\Component;
use Livewire\WithPagination;

class AllocationsSheet extends Component
{
    use WithPagination;

    public $business;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.allocations-sheet');
    }
}
