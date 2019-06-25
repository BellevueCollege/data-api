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
    return ''; //Return blank for main URL
});

Route::group(['domain' => config('dataapi.api_internal_domain')], function ($router) {

    Route::get('admin/login', ['as' => 'admin.login', 'uses' => 'AdminController@loginShow']);
    Route::post('admin/login', 'AdminController@loginPost');

});

Route::group(['domain' => config('dataapi.api_internal_domain'), 'middleware' => 'auth:admin'], function ($router) {

    Route::get('admin', ['as' => 'admin.index', 'uses' => 'AdminController@index']);
    Route::get('admin/client/add', ['as' => 'admin.client.add', 'uses' => 'AdminController@addClientShow']);
    Route::post('admin/client/add', ['as' => 'admin.client.add', 'uses' => 'AdminController@addClientPost']);
    Route::get('admin/client/{id}/delete', ['as' => 'admin.client.delete', 'uses' => 'AdminController@deleteClient']);
    Route::get('admin/logout', ['as' => 'admin.logout', 'uses' => 'AdminController@logout']);

});