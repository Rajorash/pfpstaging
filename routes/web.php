<?php

use App\Http\Controllers\AllocationCalculatorController;
use App\Http\Controllers\AllocationsCalendar;
use App\Http\Controllers\AllocationsController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankAccountEntryController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/', function () {
        return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    // Business routes
    Route::get('/business', 'BusinessController@index')->name('businesses');
    Route::get(
        '/business/{business}',
        [BusinessController::class, 'show']
    );

    // User routes
    Route::get(
        '/user',
        [UserController::class, 'index']
    )->name('users');
    Route::post(
        '/user',
        [UserController::class, 'store']
    );
    Route::get(
        '/user/create',
        [UserController::class, 'create']
    )->name('users.create');
    Route::get(
        '/user/{user}',
        [UserController::class, 'show']
    );
    Route::get(
        '/user/edit/{user}',
        [UserController::class, 'edit']
    )->name('users.edit');
    Route::put(
        '/user/{user}',
        [UserController::class, 'update']
    )->name('users.update');

    // Bankaccount routing
    Route::resource(
        'business.accounts', 'BankAccountController'
    );
    Route::get(
        '/accounts/{account}/create-flow',
        [BankAccountController::class, 'createFlow']
    );
    Route::post(
        '/accounts/{account}/create-flow',
        [BankAccountController::class, 'storeFlow']
    );
    Route::delete(
        '/accounts/{account}/flow/{flow}',
        [BankAccountController::class, 'destroyFlow']
    );
    Route::get(
        '/accounts/{account}/flow/{flow}/edit',
        [BankAccountController::class, 'editFlow']
    );
    Route::put(
        '/accounts/{account}/flow/{flow}',
        [BankAccountController::class, 'updateFlow']
    );

    Route::get(
        '/business/{business}/tax', 'TaxRateController@index'
    );
    Route::post(
        '/taxrate', 'TaxRateController@store'
    );

    // account balance entries.
    Route::get(
        '/business/{business}/account-entry',
        [BankAccountEntryController::class, 'edit']
    );
    Route::patch(
        '/business/{business}/account-entry',
        [BankAccountEntryController::class, 'update']
    );

    // Allocation Calculator
    Route::get(
        '/calculator',
        [AllocationCalculatorController::class, 'index'])->name('allocation-calculator');

    // Projection forecast tool
    //
    // Projection Forecast data entry routing (formerly labeled as allocation calculator) routing.
    Route::get(
        '/allocations',
        [AllocationsController::class, 'index']
    )->name('allocations');
    Route::get(
        '/allocations/{business}',
        [AllocationsController::class, 'allocations']
    );

    // Rollout Percentages routing
    Route::get(
        '/business/{business}/percentages',
        [AllocationsController::class,
        'percentages']
    )->name('allocations-percentages');
    Route::post('/business/percentages/ajax/update',
        [AllocationsController::class, 'updatePercentages']
    )->name('allocations-percentages-update');

    Route::post('/allocations/update',
        [AllocationsController::class, 'updateAllocation']
    );
    Route::post('/percentages/update',
        [AllocationsController::class, 'updatePercentage']
    );

    // Projections
    Route::get(
        '/business/{business}/projections',
        [ProjectionController::class ,'index']
    )->name('projections');
    Route::post(
        '/business/projections/ajax/update',
        [ProjectionController::class ,'updateData']
    )->name('projections-controller-update');

    //ajax calls
    Route::get(
        '/business/{business}/allocations_calendar',
        [AllocationsCalendar::class, 'calendar']
    )->name('allocations-calendar');
    Route::post(
        '/business/allocations_calendar/ajax/update',
        [AllocationsCalendar::class, 'updateData']
    )->name('allocations-controller-update');
});

Route::middleware(
    ['auth:sanctum', 'verified']
)->get(
    '/dashboard',
    function () {
        return view('dashboard');
    }
)->name('dashboard');
