<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::post('register', 'RegisterController@index');


Route::middleware(['auth:api'])->group(function () {
    Route::get('test', 'TestController@index');
    Route::post('vin-identify', 'VinIdentifyController@index');
    Route::get('vin-query', 'VinIdentifyController@vinQuery');
    Route::get('file/qiniu-params', 'FileController@getQiniuParams');
    Route::post('car/save-car', 'CarController@saveCar');
    Route::post('car/del-maintenance-record', 'CarController@delMaintenanceRecord');
    Route::post('car/del-img', 'CarController@delImg');
    Route::get('draft', 'DraftController@index');
    Route::post('draft', 'DraftController@store');

});
Route::get('cars', 'CarController@index');
Route::get('car-filters', 'CarController@filters');
Route::get('cars/{id}', 'CarController@show');
Route::get('car/img-groups', 'CarController@imgGroups');
Route::get('car/spec', 'CarController@carSpec');
Route::get('area/provinces', 'AreaController@provinces');
Route::get('cartags', 'CartagController@index');
Route::get('car/maintenance-records', 'CarController@maintanenceRecords');
Route::get('test-noauth', 'TestController@index');


