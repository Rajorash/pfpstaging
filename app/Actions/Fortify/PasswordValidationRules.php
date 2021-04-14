<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Rules\Password;
use Olssonm\Zxcvbn\Facades\Zxcvbn;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return ['required', 'string', new Password, 'confirmed', 'zxcvbn_min:3' ];
    }
}
