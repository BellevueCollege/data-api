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

Route::get('admin/login', ['as' => 'admin.login', 'uses' => 'AdminController@loginShow']);
Route::post('admin/login', 'AdminController@loginPost');

Route::group(['middleware' => 'auth:admin'], function ($router) {

    Route::get('admin', ['as' => 'admin.index', 'uses' => 'AdminController@index']);
    Route::get('admin/add', 'AdminController@addClient');

});