<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use LivewireUI\Modal\ModalComponent;

class ModalConfirmDelete extends ModalComponent
{
    public string $flowMessage;
 

    protected $listeners = [
        'confirmDelete' => 'confirmDelete'
    ];

    /**
     * @param  int  $flowId
     * @param  int  $accountId
     */
    public function mount()
    {
       
        $this->flowMessage = "Are You Sure You Want To Delete Your Business!!";
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Redirector
     */
    public function confirmDelete()
    {
        $this->closeModal();
        // $this->forceClose()->closeModal();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.modal-confirm-delete');
    }
}
