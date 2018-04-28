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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => 'v1'], function () {
    Route::post('checkMobile', 'api\v1\ApiController@checkMobile');
    Route::post('checkEmail', 'api\v1\ApiController@checkEmail');
    Route::post('customerLoginOrRegister', 'api\v1\ApiController@customerLoginOrRegister');
    Route::post('checkLoginCode', 'api\v1\ApiController@checkLoginCode');


});

Route::group(['prefix' => 'v2'], function () {


});
