<?php

namespace App\Http\Livewire;

use App\Models\AccountFlow;
use App\Models\BankAccount;
use LivewireUI\Modal\ModalComponent;

class ModalConfirmDelete extends ModalComponent
{
    public string $flowMessage;
    public $iWouldLikeToDelete = false;

    protected $listeners = [
        'confirmDelete' => 'confirmDelete',
        'falseModal' => 'falseModal'
    ];

    /**
     * @param  int  $flowId
     * @param  int  $accountId
     */
    public function mount()
    {
        $this->flowMessage = "Do you want to export any data before you delete the business?";
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Redirector
     */
    public function confirmDelete()
    {
        $this->iWouldLikeToDelete = 1;
        session(['iWouldLikeToDelete' => $this->iWouldLikeToDelete]);
        $this->closeModal();
    }

    public function falseModal()
    {
        $this->iWouldLikeToDelete = 0;
        session(['iWouldLikeToDelete' => $this->iWouldLikeToDelete]);
        $this->closeModal();
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.modal-confirm-delete');
    }
}
