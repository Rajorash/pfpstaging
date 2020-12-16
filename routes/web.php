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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/business', 'BusinessController@index')->name('businesses');
    Route::get('/business/{business}', 'BusinessController@show');

    Route::resource('business.accounts', 'BankAccountController');
    Route::get('/accounts/{account}/create-flow', 'BankAccountController@createFlow');
    Route::post('/accounts/{account}/create-flow', 'BankAccountController@storeFlow');
    Route::delete('/accounts/{account}/flow/{flow}', 'BankAccountController@destroyFlow');
    Route::get('/accounts/{account}/flow/{flow}/edit', 'BankAccountController@editFlow');
    Route::put('/accounts/{account}/flow/{flow}', 'BankAccountController@updateFlow');

    Route::get('/user', 'UserController@index')->name('users');
    Route::post('/user', 'UserController@store');
    Route::get('/user/create', 'UserController@create');
    Route::get('/user/{user}', 'UserController@show');

    Route::get('/business/{business}/tax', 'TaxRateController@index');
    Route::post('/taxrate', 'TaxRateController@store');
    // account balance entries.
    Route::get('/business/{business}/account-entry', 'BankAccountEntryController@edit');
    Route::patch('/business/{business}/account-entry', 'BankAccountEntryController@update');
    // allocation calculator routing.
    Route::get('/allocations', 'AllocationsController@index')->name('allocations');
    Route::get('/allocations/{business}', 'AllocationsController@allocations');
    Route::get('/allocations/{business}/percentages', 'AllocationsController@percentages');
    Route::post('/allocations/update', 'AllocationsController@updateAllocation');
    Route::post('/percentages/update', 'AllocationsController@updatePercentage');

});
