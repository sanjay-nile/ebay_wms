<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::namespace('Api')->group(function () {
    Route::group(['middleware' => 'api'], function ($router) {
        //Login Api
        Route::post('signin', 'ApiLoginController@postSignin');     
        Route::post('change-password', 'ApiLoginController@changePassword')->middleware('auth.jwt');
        Route::post('forget-password', 'ApiRegisterController@forgetPassword');

        Route::group(['middleware'=>'auth.jwt'],function(){
            // Client Routes
            Route::get('client/dashboard','ApiOpreatorController@dashboard');
            Route::get('client/profile','ApiOpreatorController@clientProfile');

            Route::get('client/scan-in/list','ApiOpreatorController@scanInList');
            Route::get('client/scan-out/list','ApiOpreatorController@scanOutList');
            Route::get('client/combined-scan-out/list','ApiOpreatorController@combinedScanOutList');
            Route::get('client/dispatch/list/{status}','ApiOpreatorController@dispatchList');

            Route::get('client/all-scan/list','ApiOpreatorController@getAllScanDataList');

            Route::get('location/list','ApiOpreatorController@locationList');
            Route::get('cancel/list','ApiOpreatorController@cancelledList');
            Route::get('location/move/list','ApiOpreatorController@moveLocationList');


            Route::post('scan-in/store', 'ApiOpreatorController@scanInStore');
            Route::post('scan-out/store', 'ApiOpreatorController@scanOutStore');
            Route::post('dispatch/store', 'ApiOpreatorController@dispatchPackageStore');
            Route::post('cancel/package/store', 'ApiOpreatorController@cancelPackage');
            Route::post('location/move/store', 'ApiOpreatorController@moveLocationStore');

            Route::post('combind/generate/label', 'ApiOpreatorController@combinedCancelPackage');
            Route::get('order/detail/{status}','ApiOpreatorController@ebayNewOrderDetailContent');

            Route::post('client/profile/{id}/update','ApiClientController@updateClientProfile');
            Route::post('client/address/{id}/update','ApiClientController@updateClientAddress');
            Route::get('client/reverse-logistic','ApiClientController@clientReverseLogisticList');
            Route::get('client/user','ApiClientController@clientUserList');

            // Common Routes    
            Route::post('warehouse/add', 'ApiCommonController@postWarehouse');
            Route::delete('warehouse/{id}', 'ApiCommonController@deleteWarehouse');     
            Route::get('state', 'ApiCommonController@getStateListByCountryId');     
            Route::get('country', 'ApiCommonController@getCountryList');
            Route::post('client/user/add', 'ApiCommonController@storeClientUser');
            Route::post('client/user/{id}/update', 'ApiCommonController@updateClientUser');
            Route::delete('client/user/{id}', 'ApiCommonController@deleteClientUser');
            Route::get('client-list', 'ApiCommonController@getClientList');
        });
    });
});