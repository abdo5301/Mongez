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

Route::get('/', 'WebController@index')->name('web.web.index');
Route::get('/{slug}', 'RequestController@request')->name('web.request.view');
Route::get('/{slug}/{id?}', 'RequestController@requestProperty')->name('web.request.property');
