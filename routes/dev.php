<?php

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Mail\MailVerification;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Developer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register developer routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. These should be used for views and
| functionality related to development!
|
*/

/*
|--------------------------------------------------------------------------
| Mail Routes
|--------------------------------------------------------------------------
|
| These routes can be used to check the rendering of mail during
| development without requiring constant resends to Mailtrap or other
| services.
|
| To have the email show in the list at /mail you should fill the details
| in the array in the first route. Each mail has an entry for name, notes
| and url. Please note that the 'mail' prefix is not required as it is
| added in the template.
|
*/
Route::prefix('mail')->group(function () {

    Route::view('', 'dev.mail', [
        'emails' => [
            [
                'name' => 'Verification Email',
                'notes' => 'Sent to user on account creation',
                'url' => 'verification'
            ],
        ],
    ]);

    Route::get('verification', function () {

        $client = User::whereHas(
            'roles', function($query) {
                $query->where('name', 'client');
            }
        )->first();
        $advisor = User::whereHas(
            'roles', function($query) {
                $query->where('name', 'advisor');
            }
        )->first();

        return new MailVerification(
            $client,
            $advisor,
            Str::random()
        );

    });

});
