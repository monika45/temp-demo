<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

//    $router->get('/', 'HomeController@index')->name('home');
//    $router->get('/', 'StpUserController@index')->name('home');
    $router->get('/', 'StpController@dashboard')->name('home');
    // stp后台页面(旧的)
    $router->resource('stp-users', StpUserController::class);
    $router->get('stp-user-monitor-datas/{id}', 'StpUserMonitorDataController@index');


    // STP Backoffice
    $router->get('dashboard', 'StpController@dashboard');
    $router->get('user-records', 'StpController@userRecords');
    $router->get('user-information', 'StpController@userInfomation');

    // STP Backoffice api
    $router->get('api/temperatureMapData', 'StpUserMonitorDataController@temperatureMapData');
    $router->get('api/abnormalTopCities', 'StpUserMonitorDataController@abnormalTopCities');
    $router->get('api/dataRecords', 'StpUserMonitorDataController@dataRecords');
    $router->get('api/usersRecord', 'StpUserMonitorDataController@usersRecord');
});
