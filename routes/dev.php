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

// Email routes
Route::prefix('mail')->group(function () {

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
