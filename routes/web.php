<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('optimize:clear');
    return '<h1>Cache facade value cleared</h1>';
});

Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate');
    return '<h1>migrate success</h1>';
});

Route::get('/', function () {
    return view('pages.frontend.home.index', array('template' => 'welcome', 'user' => ''));
})->name('home');

Route::group(['prefix' => 'admin', 'namespace'=>'Admin'], function () {
    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    // Route::get('/', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login')->name('admin.login.submit');
    Route::post('logout', 'LoginController@logout')->name('admin.logout');

    // Registration Routes...
    Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'RegisterController@create');

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');
});

Auth::routes();

Route::group(['as'=>'admin.','prefix' => 'admin','namespace'=>'Admin'], function () {
    //admin Dashboard
    Route::get('admin-dashboard', 'AdminController@dashboard')->name('dashboard');
    Route::get('metric-dashboard', 'AdminController@metricDashboard')->name('metric.dashboard');
    Route::get('graph-dashboard', 'AdminController@graphDashboard')->name('graph.dashboard');

    Route::get('runtime-dashboard', 'AdminController@runtimeDashboard')->name('runtime.dashboard');
    Route::get('live-graph-dashboard', 'AdminController@liveGraphDashboard')->name('live.graph.dashboard');
    Route::get('chart-data', 'AdminController@chartData')->name('chart.data');
    
    Route::get('change-password', 'AdminController@changePassword')->name('change-password');
    Route::put('change-password/{user}/update', 'AdminController@changePasswordUpdate')->name('change-password.update');
    Route::get('profile', 'AdminController@profileShow')->name('profile');
    Route::put('profile/{user}/update', 'AdminController@profileUpdate')->name('profile.update');

    //Sub Admin Routes
    Route::get('sub-admin-rep/list','AdminController@index')->name('sub-admin');
    Route::get('sub-admin-rep/create','AdminController@createForm')->name('sub-admin.create');
    Route::post('sub-admin-rep','AdminController@storeSubAdmin')->name('sub-admin.store');
    Route::get('sub-admin-rep/{user}/edit','AdminController@editSubAdmin')->name('sub-admin.edit');
    Route::put('sub-admin-rep/{user}/update','AdminController@updateSubAdmin')->name('sub-admin.update');
    Route::get('sub-admin-rep/{id}', 'AdminController@subAdminDestory')->name('sub-admin.destory');

    // Sub Admin Access routes
    Route::get('customer-rep-dashboard', 'SubAdminController@dashboard')->name('sub-admin.dashboard');
    Route::get('operator-dashboard', 'SubAdminController@dashboard')->name('operator.dashboard');

    // Admin Client Routes...
    Route::get('client/list', 'ClientController@index')->name('client');
    Route::get('client/create', 'ClientController@createForm')->name('client.create');
    Route::post('client/save', 'ClientController@ClientStore')->name('client.store.new');
    Route::get('client/{user}/edit', 'ClientController@editClient')->name('client.edit');
    Route::post('client/store', 'ClientController@storeClient')->name('client.store');

    Route::get('assign-warehouse','ClientController@assignWarehouse')->name('client.assignwarehouse');
    Route::post('assign-warehouse-store','ClientController@assignWarehouseStore')->name('client.assignwarehouse.store');

    Route::put('client/{user}/update', 'ClientController@updateClient')->name('client.update');
    Route::get('client/{id}', 'ClientController@clientDestory')->name('client.destory');
    Route::get('client-dashboard', 'ClientController@dashboard')->name('client.dashboard');
    Route::get('client-order', 'ClientController@getClientOrders')->name('client-order');
    Route::get('client-profile', 'ClientController@getClientProfile')->name('client.profile');

    Route::get('warehouse/{id?}','OrderController@getWarehouseList')->name('warehouse');
    Route::post('add/warehouse','OrderController@addWarehouse')->name('add.warehouse');

    // code by sanjay
    Route::get('country-list', 'ClientController@getCountryList')->name('country-list');
    Route::get('carrier-list', 'ClientController@getCarrierList')->name('carrier-list');
    Route::get('shipment-list', 'ClientController@getShipmentList')->name('shipment-list');
    Route::get('charges-list', 'ClientController@getchargesList')->name('charges-list');

    # carrier routes...
    Route::get('carrier/{id?}', 'MasterController@carrierList')->name('carrier');
    Route::post('carrier/store', 'MasterController@carrierPost')->name('carrier.store');
    Route::get('carrier/delete/{id}', 'MasterController@carrierDestroy')->name('carrier-delete');

    Route::get('other-charges', 'MasterController@otherChargesList')->name('other-charges');
    Route::post('other-charges/store', 'MasterController@otherChargesPost')->name('other-charges.store');
    Route::get('other-charges/delete/{id}', 'MasterController@otherChargesDestroy')->name('other-charges-delete');

    Route::get('state-list', 'MasterController@stateList')->name('state-list');
    Route::post('state-list/store', 'MasterController@statePost')->name('state-list.store');
    Route::get('state-list/delete/{id}', 'MasterController@stateDestroy')->name('state-list-delete');


    //Sub Admin Routes
    Route::get('operator/list','AdminController@operator')->name('operator');
    Route::get('operator/create','AdminController@operatorCreateForm')->name('operator.create');
    Route::post('operator','AdminController@storeOperator')->name('operator.store');
    Route::get('operator/{user}/edit','AdminController@editOperator')->name('operator.edit');
    Route::put('operator/{user}/update','AdminController@updateOperator')->name('operator.update');
    Route::get('operator/{id}', 'AdminController@operatorDestory')->name('operator.destory');

    # rack routes...
    Route::get('rack/list','WarehouseController@getRackLists')->name('rack.list');
    Route::get('add/rack/{id?}','WarehouseController@addRack')->name('add.rack');
    Route::post('rack/store','WarehouseController@rackStore')->name('rack.store');
    Route::post('import/rack','WarehouseController@importRack')->name('import.rack');
    Route::get('location/rack-label/{id?}','WarehouseController@redirectLocationInvoice')->name('location.invoice');
    Route::post('location/rack-label/','WarehouseController@bulkLocationInvoice')->name('location.bulk.invoice');


    # scan in ..
    Route::get('scan-in','WarehouseController@scanInList')->name('add.scan.in');
    Route::get('all-scan-data','WarehouseController@getAllScanDataList')->name('all.scan.data');
    Route::post('scan-in/store','WarehouseController@scanInStore')->name('store.scan.in');

    # scan out...
    Route::get('scan-out','WarehouseController@scanOutList')->name('add.scan.out');
    Route::post('scan-out/store','WarehouseController@scanOutStore')->name('store.scan.out');

    Route::get('combined/scan-out','WarehouseController@combinedScanOutList')->name('combined.scan.out');

    Route::get('dispatch/{status}','WarehouseController@dispatchList')->name('dispatch.list');
    Route::post('dispatch/store','WarehouseController@dispatchPackageStore')->name('store.dispatch');
    
    Route::get('package/image/{id?}','WarehouseController@redirectPackageImage')->name('package.image');
    Route::get('remove/package/{id?}','WarehouseController@removePackage')->name('remove.package');

    Route::get('move/location','WarehouseController@moveLocationList')->name('move.location.list');
    Route::post('move/location/store','WarehouseController@moveLocationStore')->name('store.move.location');

    Route::get('remove/order-package/{id}','OrderController@removeOrderPackage')->name('remove.order.package');

    Route::get('direct/ebay/orders/{id?}/{url?}', 'OrderController@getOrdersFromEbay')->name('fetch.order.ebay');
    Route::get('direct/cancel/ebay/orders/{id?}/{url?}', 'OrderController@getCancelOrdersFromEbay')->name('cancel.fetch.order.ebay');

    Route::get('cancelled/ebay/orders/{id}','VendorController@cancelledEbayOrder')->name('cancelled-ebay-orders');
    Route::get('cancel/ebay/orders/{id}','WarehouseController@cancelledOrder')->name('cancel-ebay-orders');
    Route::get('ebay/order/invoice/{order_id}','VendorController@redirectOrderInvoice')->name('order_invoice');

    Route::get('cancelled','WarehouseController@cancelledList')->name('cancelled.list');
    Route::post('combined/cancel/package','WarehouseController@combinedCancelPackage')->name('combined.dispatch');

    Route::get('ebay/new-orders/details/{order_id}','VendorController@ebayNewOrderDetailContent')->name('new_ebay_order_details');
    Route::get('ebay/orders/details/{order_id}','VendorController@ebayOrderDetailsPageContent')->name('view_ebay_order_details');

    Route::post('generatelabel', 'VendorController@generateLabel')->name('generateLabel');

    Route::get('sync/location/data','WarehouseController@syncLocationData')->name('sync.location.data');
    Route::get('sync/all/location/data','WarehouseController@cronSyncLocationData')->name('cron.sync.location.data');

    Route::get('change/package/data', 'WarehouseController@changePackageData')->name('change.package.data');
    Route::post('change/package/store', 'WarehouseController@changePackageStore')->name('change.package.store');

    Route::post('assign/operator','WarehouseController@assignOperatorToItem')->name('assign.operator');

    Route::get('move/to/dispatch','WarehouseController@moveScanOutToDispatch')->name('move.to.dispatch');
    Route::post('move/dispatch/store','WarehouseController@moveDispatchedStore')->name('store.move.dispatch');

    Route::get('sync/update/location','WarehouseController@cronSyncUpdateLocation')->name('cron.sync.update.location');
});

Route::group(['as'=>'user.','prefix' => 'user','namespace'=>'User', 'middleware' => ['auth', 'user']], function () {
    Route::get('dashboard', 'UserDashboardController@index')->name('dashboard');
});

//Common Route for Web and Admin
Route::namespace ('Common')->group(function () {
    Route::get('/country/state', 'CommonController@getStateByCountryId')->name('country.state');
    Route::get('warehouse', 'CommonController@warehouseForm')->name('warehouse.create');
    Route::get('/warehouse/{id}', 'CommonController@getWarehouse')->name('warehouse.show');
    Route::get('sendmail/{id}', 'CommonController@sendCustomDutyMail')->name('custom.duty.mail');
    Route::get('get/carrier-product', 'CommonController@getCarrierProductAndService')->name('carrier.product');

    Route::get('check-mail', 'CommonController@checkMailContent')->name('check.mail');

    Route::post('/warehouse/store', 'CommonController@postWarehouse')->name('warehouse.store');
    Route::post('ajax-image-upload', 'CommonController@ajaxImage')->name('ajax-image-upload');
    Route::post('package-image-upload', 'CommonController@packageAjaxImage')->name('package-image-upload');
    Route::post('save-pallet-id', 'CommonController@savePallet')->name('save-pallet-id');
    Route::post('remove-image', 'CommonController@removeImage')->name('remove-image');
    Route::post('update-package', 'CommonController@updatePackage')->name('update-package');
    Route::post('change-rtn-option', 'CommonController@changOrderReturnOption')->name('order.rtn.option');


    //Delete client Shipment && Other Charges
    Route::delete('/client-shipment-charges/{id}', 'CommonController@deleteClientShipmentOtherCharges')->name('client-shipment-other-charges.delete');
    Route::delete('/warehouse/{id}', 'CommonController@deleteWarehouse')->name('warehouse.delete');

    # Create Waybill...
    Route::group(['middleware' => ['auth:admin']], function () {
        Route::post('admin-waybill-store', 'CommonController@storeWayBill')->name('admin-waybills.store');
        Route::post('admin-bulk-upload', 'CommonController@bulkUploadWaybills')->name('admin-waybills.bulk-upload');
        Route::post('admin-waybill-update', 'CommonController@customerWaybillUpdate')->name('admin-waybills.update');
        Route::post('return-bar/order', 'CommonController@returnBarOrder')->name('return-bar.order');
    });

    Route::group(['middleware' => ['client']], function () {
        Route::post('client/client-waybill-store', 'CommonController@storeWayBill')->name('client-waybills.store');
        Route::post('client-bulk-upload', 'CommonController@bulkUploadWaybills')->name('client-waybills.bulk-upload');
        Route::post('client-waybill-update', 'CommonController@customerWaybillUpdate')->name('client-waybills.update');
        Route::post('client/return-bar/order', 'CommonController@returnBarOrder')->name('client.return-bar.order');
    });

    Route::group(['middleware' => ['clientUser']], function () {
        Route::post('client-user-waybill-store', 'CommonController@storeWayBill')->name('client-user-waybills.store');
        Route::post('client-user-bulk-upload', 'CommonController@bulkUploadWaybills')->name('client-user-waybills.bulk-upload');
    });

    # get tracking detail
    Route::get('get-tracking/{id}', [
        'uses' => 'CommonController@GetTrackingById',
        'as'   => 'admin.get-tracking',
    ]);

    Route::get('return-bar', 'CommonController@getReturnBar')->name('return-bar');

    Route::get('get-more-tracking/{id}', [
        'uses' => 'CommonController@GetMoreTrackingById',
        'as'   => 'admin.get-more-tracking',
    ]);

    # update order claim id
    Route::post('update/order/shipment', 'CommonController@updateOrderShipment')->name('update-shipment');
});