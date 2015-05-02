<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::controller('login', 'LoginController');
Route::get('logout', 'LoginController@getLogout');

Route::group(array('before' => 'auth'), function()
{
    Route::get('programmes/{id}/refresh', 'ProgrammeController@refresh');
    Route::get('programmes/{id}/fetch', 'ProgrammeController@fetch');
});

Route::resource('entries', 'EntryController');
Route::get('programmes/{id}.xml', 'ProgrammeController@rss');
Route::resource('programmes', 'ProgrammeController');
