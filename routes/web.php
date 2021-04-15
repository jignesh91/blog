<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register','Auth\AuthController@register')->name('register');
Route::post('/register','Auth\AuthController@storeUser');

Route::get('/login', 'Auth\AuthController@login')->name('login');
Route::post('/login', 'Auth\AuthController@authenticate');

Route::get('/logout','Auth\AuthController@logout')->name('logout');

Route::get('/home','Auth\AuthController@home')->name('home');

Route::any('post/data','PostController@data')->name('post.data');
Route::resource('/post','PostController');
Route::any('post-delete/{id}','PostController@delete');
Route::any('post-view/{id}','PostController@show');