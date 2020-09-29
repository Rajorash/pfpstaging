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
    Route::get('/business', 'BusinessController@index');
    Route::get('/business/{business}', 'BusinessController@show');
    
    Route::resource('business.accounts', 'BankAccountController');
    Route::get('/accounts/{account}/create-flow', 'BankAccountController@createFlow');
    Route::post('/accounts/{account}/create-flow', 'BankAccountController@storeFlow');
    Route::delete('/accounts/{account}/flow/{flow}', 'BankAccountController@destroyFlow');

    Route::get('/user', 'UserController@index');
    Route::post('/user', 'UserController@store');
    Route::get('/user/create', 'UserController@create');
    Route::get('/user/{user}', 'UserController@show');

    Route::get('/allocations/percentages', function () {
        $accounts = [
            ['label' => 'Profit', 'percentage' => 35],
            ['label' => 'Opex', 'percentage' => 50],
            ['label' => 'General', 'percentage' => 15]
        ];
        return view('allocations.percentages', ['accounts' => $accounts]);
    });

});