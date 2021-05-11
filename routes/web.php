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

use Illuminate\Support\Facades\Auth;

Route::group(['middleware' => 'guest'], function () {
    Route::post('/register', 'Auth\RegisterController@register')->name('register');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/user', fn () => Auth::user())->name('user');
    Route::post('/photos', 'PhotoController@create')->name('photo.create');
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
});

Route::get('/photos', 'PhotoController@index')->name('photo.index');
Route::get('/photos/{photo}/download', 'PhotoController@download');

Route::get('/{any?}', fn () => view('index'))->where('any', '.+');
