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
    echo config('app.name');die;
    return view('welcome');
});

Route::group([
    'prefix' => 'stp'
], function ($route) {
    Route::post('user/login', 'StpUserController@login');
    Route::get('user/mydata', 'StpUserController@getUserData');
    Route::post('user/mydata', 'StpUserController@modifyUserData');
    Route::post('submit-temperature', 'StpUserController@submitTemperature');
    Route::get('monitor-datas', 'StpUserController@monitorDataList');
    Route::get('daily-temperatures', 'StpUserController@dailyTemperatures');
    Route::post('del-monitor-data', 'StpUserController@delMonitorData');
    Route::get('seed', 'TestController@seedData');
});



