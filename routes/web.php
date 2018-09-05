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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'DashboardController@index')->name('dashboard');

// Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/dashboard', 'DashboardController@index');

// Route::get('/collections/outpatient', 'CollectionsOutpatientController@index')->name('collections.outpatient');


Route::get('collections/outpatient', 'CollectionsOutpatientController@index')->name('collections.outpatient');


Route::get('collections/outpatient/getdata', 'CollectionsOutpatientController@getdata')->name('collections.outpatient.getdata');


Route::get('collections/outpatient/create', 'CollectionsOutpatientController@create')->name('collections.outpatient.create');


Route::post('collections/outpatient/create/payment', 'CollectionsOutpatientController@store');


Route::get('collections/outpatient/action', 'CollectionsOutpatientController@action')->name('collections.outpatient.action');


Route::get('collections/outpatient/getCustomFilterData', 'CollectionsOutpatientController@getCustomFilterData')->name('collections.outpatient.getCustomFilterData');


Route::post('collections/outpatient/create', 'CollectionsOutpatientController@show')->name('collections.outpatient.create.show');


Route::get('/collections/outpatient/create/showcharge/{id}', [

	'uses'	=>	'CollectionsOutpatientController@edit',
	'as'	=>	'collections.outpatient.edit'

]);




