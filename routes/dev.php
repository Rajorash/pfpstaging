<?php

use App\Mail\AdvisorChangedCountOfLicenses;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Mail\MailVerification;
use App\Mail\SendBusinessCollaborateNotification;
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
| Utility Variables
|--------------------------------------------------------------------------
|
| Variabnles set here for instantiating various dev functions in the
| routes below.
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

    Route::view('', 'dev.mail', [
        'emails' => [
            [
                'name' => 'Verification Email',
                'notes' => 'Sent to user on account creation',
                'url' => 'verification'
            ],
            [
                'name' => 'AdvisorChangedCountOfLicenses',
                'notes' => 'Sent to user when an advisor updates the license count',
                'url' => 'license-count-changed'
            ],
            [
                'name' => 'SendBusinessCollaborateNotification',
                'notes' => 'Sent to business owner to notify of collaboration',
                'url' => 'business-collaborate'
            ],
        ],
    ]);

    Route::get('verification', function () use ($client, $advisor) {
        return new MailVerification(
            $client,
            $advisor,
            Str::random()
        );
    });

    Route::get('license-count-changed', function () use ($client, $advisor) {
        $licenses = 7;
        $assigned = 5;
        $available = $licenses - $assigned;

        return new AdvisorChangedCountOfLicenses(
            $client,
            $advisor,
            $licenses,
            $assigned,
            $available
        );
    });

    Route::get('business-collaborate', function () use ($advisor) {

        $title = 'Collaboration for you';
        $text = "Business <b>&quot;Business Name&quot;</b> linked to you by {$advisor->name}";

        return new SendBusinessCollaborateNotification
        (
            $advisor->name,
            "Business Name",
            $title,
            $text
        );
    });



});
