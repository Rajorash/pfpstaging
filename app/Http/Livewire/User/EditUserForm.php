<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;

class EditUserForm extends Component
{
    public User $user;
    public $saved = false;

    protected $rules = [
        'user.name' => 'required|min:6',
        'user.email' => [
            'required',
            'email',
            'unique:users,email'
        ],
        'user.title' => 'nullable|string|min:3',
        'user.responsibility' => 'nullable|string|min:4',
        'user.timezone' => 'sometimes|timezone',
        // 'user.roles' => 'required',
    ];


    /**
     * Magic function fires when a property updates on
     * the form. Used mostly for instant validation
     * feedback.
     *
     * @param [type] $propertyName
     * @return void
     */
    public function updatedUser($value, $updatedKey)
    {
        $this->validate($this->user->updatedKey);
    }

    /**
     * Validate the data and then save the user model to the database.
     *
     * @return void
     */
    public function save()
    {
        $this->dispatchBrowserEvent('user-saved');

        $this->validate();

        $this->user->save();

        $this->saved = true;
        // used for responsiveness eg closing modals etc. Not implemented yet
        $this->dispatchBrowserEvent('user-saved');

        // return redirect("user");

    }

    public function render()
    {
        return view('user.edit-user-form');
    }
}
