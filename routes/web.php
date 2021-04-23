<?php

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

use App\Http\Controllers\AllocationCalculatorController;
use App\Http\Controllers\AllocationsCalendar;
use App\Http\Controllers\AllocationsController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/business', 'BusinessController@index')->name('businesses');
    Route::get('/business/{business}', 'BusinessController@show');

    Route::get('/user', 'UserController@index')->name('users');
    Route::post('/user', 'UserController@store');
    Route::get('/user/create', 'UserController@create')->name('users.create');
    Route::get('/user/{user}', 'UserController@show');
    Route::get('/user/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('users.update');

    Route::resource('business.accounts', 'BankAccountController');
    Route::get('/accounts/{account}/create-flow', 'BankAccountController@createFlow');
    Route::post('/accounts/{account}/create-flow', 'BankAccountController@storeFlow');
    Route::delete('/accounts/{account}/flow/{flow}', 'BankAccountController@destroyFlow');
    Route::get('/accounts/{account}/flow/{flow}/edit', 'BankAccountController@editFlow');
    Route::put('/accounts/{account}/flow/{flow}', 'BankAccountController@updateFlow');

    Route::get('/business/{business}/tax', 'TaxRateController@index');
    Route::post('/taxrate', 'TaxRateController@store');

    // account balance entries.
    Route::get('/business/{business}/account-entry', 'BankAccountEntryController@edit');
    Route::patch('/business/{business}/account-entry', 'BankAccountEntryController@update');

    // Allocation Calculator
    Route::get('/calculator', [AllocationCalculatorController::class, 'index'])->name('allocation-calculator');

    // Projection forecast tool
    //
    // Projection Forecast data entry routing (formerly labeled as allocation calculator) routing.
    Route::get('/allocations', 'AllocationsController@index')->name('allocations');
    Route::get('/allocations/{business}', 'AllocationsController@allocations');

    // Rollout Percentages routing
    Route::get('/allocations/{business}/percentages',
        [AllocationsController::class, 'percentages'])->name('allocations-percentages');
    Route::post('/allocations/percentages/ajax/update',
        [AllocationsController::class, 'updatePercentages'])->name('allocations-percentages-update');

    Route::post('/allocations/update', 'AllocationsController@updateAllocation');
    Route::post('/percentages/update', 'AllocationsController@updatePercentage');

    // Projections
    Route::get('/projections/{business}', [ProjectionController::class ,'index'])->name('projections');
    Route::post('/projections/ajax/update', [ProjectionController::class ,'updateData'])->name('projections-controller-update');

    //ajax calls
    Route::get('/business/{business}/allocations_calendar',
        [AllocationsCalendar::class, 'calendar'])->name('allocations-calendar');
    Route::post('/business/allocations_calendar/ajax/update',
        [AllocationsCalendar::class, 'updateData'])->name('allocations-controller-update');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
