<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('app/getStatus', 'StatusController@index');
Route::post('app/login', 'App\LoginController@login');
Route::group(['prefix' => 'app', 'namespace' => 'App', 'middleware' => 'auth:api'], function () {
    Route::get('/getUserData', 'EmployeeController@getUserData');
    Route::get('/getAttendance', 'EmployeeController@getAttendance');
    Route::post('/checkIn', 'AttendanceController@checkIn');
    Route::post('/checkOut', 'AttendanceController@checkOut');
    Route::get('/events', 'EventController@index');
    Route::post('/checkInEvent', 'EventAttendanceController@checkIn');
    Route::post('/checkOutEvent', 'EventAttendanceController@checkOut');
    Route::get('/getEvents', 'EventAttendanceController@getEvents');
    Route::post('/check', 'AttendanceController@check');

    Route::get('resetAttendance', 'AttendanceController@reset');
});
