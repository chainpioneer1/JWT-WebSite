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

Auth::routes();

// Password routes
Route::post('password/reset_password', 'Auth\ResetPasswordController@reset_password');
Route::post('password/request_email', 'Auth\ResetPasswordController@request_email');
Route::get('password/update', 'Auth\ResetPasswordController@update');

Route::get('/home', 'HomeController@index')->name('home');

// profile routes
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::post('save_profile', 'HomeController@save_profile');

