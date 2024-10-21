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
Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //

Auth::routes();

Route::get('/staff/change-password', 'StaffController@changePassword')->name('system.staff.change-password');
Route::post('/staff/change-password', 'StaffController@changePasswordPost')->name('system.staff.change-password-post');




Route::resource('/lead','LeadController',['as'=>'system']); //

Route::resource('/staff','StaffController',['as'=>'system']); //
Route::resource('/client','ClientController',['as'=>'system']); //
Route::resource('/property-type','PropertyTypeController',['as'=>'system']); //
Route::resource('/data-source','DataSourceController',['as'=>'system']); //
Route::resource('/purpose','PurposeController',['as'=>'system']); //
Route::resource('/property-status','PropertyStatusController',['as'=>'system']); //
Route::resource('/property-model','PropertyModelController',['as'=>'system']); //
Route::resource('/request-status','RequestStatusController',['as'=>'system']); //



Route::post('/property/remove-image','PropertyController@removeImage')->name('system.property.remove-image'); //
Route::post('/property/image-upload','PropertyController@imageUpload')->name('system.property.image-upload'); //


Route::get('/property/upload-excel','PropertyController@uploadExcel')->name('system.property.upload-excel'); //
Route::post('/property/upload-excel','PropertyController@uploadExcelStore')->name('system.property.upload-excel-store'); //

Route::resource('/property','PropertyController',['as'=>'system']); //


Route::post('/request/share','RequestController@share')->name('system.request.share'); //
Route::post('/request/close-share','RequestController@closeShare')->name('system.request.close-share'); //

Route::resource('/request','RequestController',['as'=>'system']); //
Route::resource('/area-type','AreatypesController',['as'=>'system']); //
Route::resource('/area', 'AreaController',['as'=>'system']); //

// -- Setting
Route::get('/setting', 'SettingController@index')->name('system.setting.index'); //
Route::patch('/setting', 'SettingController@update')->name('system.setting.update'); //
// -- Setting

Route::get('/parameter/create/{id}','ParameterController@create')->name('system.parameter.create'); //
Route::get('/ajax','AjaxController@index')->name('system.misc.ajax'); //

Route::resource('/parameter','ParameterController',['as'=>'system']); //

// Calls
Route::resource('/call-purpose', 'CallPurposeController',['as'=>'system']); //
Route::resource('/call-status', 'CallStatusController',['as'=>'system']); //
Route::resource('/call', 'CallController',['as'=>'system']); //
// Calls

Route::resource('/importer', 'ImporterController',['as'=>'system']); //

Route::resource('/permission-group','PermissionGroupsController',['as'=>'system']); //

Route::get('/calendar','CalendarController@index')->name('system.calendar.index'); //
Route::get('/calendar/ajax','CalendarController@ajax')->name('system.calendar.ajax'); //
Route::get('/calendar/show','CalendarController@show')->name('system.calendar.show'); //
Route::post('/calendar/store','CalendarController@store')->name('system.calendar.store'); //
Route::get('/calendar/delete','CalendarController@delete')->name('system.calendar.delete'); //


Route::get('/notifications/{ID}', 'NotificationController@url')->name('system.notifications.url');
Route::get('/notifications', 'NotificationController@index')->name('system.notifications.index');

Route::get('/auth-sessions', 'AuthSessionController@index')->name('system.staff.auth-sessions');
Route::delete('/auth-sessions', 'AuthSessionController@deleteAuthSession')->name('system.staff.delete-auth-sessions');

Route::get('/activity-log/{ID}', 'ActivityController@show')->name('system.activity-log.show'); //
Route::get('/activity-log', 'ActivityController@index')->name('system.activity-log.index'); //

Route::get('/', 'SystemController@dashboard')->name('system.dashboard');