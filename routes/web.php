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
    return view('home');
})->middleware('auth');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('/gitRepo/index', 'GithubController@handleProviderCallback')->middleware('auth');

// GitHub routes
Route::get('repogithub/{provider}', 'Auth\GitHubController@redirectToProvider');
Route::get('repogithub/{provider}/callback', 'Auth\GitHubController@handleProviderCallback');
