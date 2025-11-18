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

Route::domain(config('dataapi.api_internal_domain'))->group( function( $router ) {
    Route::name('admin.')->group(function () {

        Route::get('admin/login', 'AdminController@loginShow')->name('login');
        Route::post('admin/login', 'AdminController@loginPost');

        Route::middleware(['auth:admin'])->group(function () {

            Route::get('admin', 'AdminController@index')->name('index');
            Route::get('admin/client/add', 'AdminController@addClientShow')->name('client.add');
            Route::post('admin/client/add', 'AdminController@addClientPost')->name('client.add');
            Route::delete('admin/client/{id}/delete', 'AdminController@deleteClient')->name('client.delete');
            Route::get('admin/client/{id}/update', 'AdminController@updateClient')->name('client.update');
            Route::put('admin/client/{id}/update', 'AdminController@updateClientPut')->name('client.update');
            Route::get('admin/logout', 'AdminController@logout')->name('logout');

        });
    });
});

Route::get('/', function () {
    return ''; //Return blank for main URL
});
