<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Branch', 'as' => 'branch.'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', 'LoginController@captcha')->name('default-captcha');
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['branch', 'branch_status']], function () {
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');
        Route::get('settings', 'DashboardController@settings')->name('settings');
        Route::post('settings', 'DashboardController@settings_update');
        Route::post('settings-password', 'DashboardController@settings_password_update')->name('settings-password');
        Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
        Route::get('/get-restaurant-data', 'SystemController@restaurant_data')->name('get-restaurant-data');
        Route::get('order-statistics', 'DashboardController@order_statistics')->name('order-statistics');
        Route::get('earning-statistics', 'DashboardController@earning_statistics')->name('earning-statistics');

        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/', 'POSController@index')->name('index');
            Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
            Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
            Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
            Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
            Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
            Route::post('tax', 'POSController@update_tax')->name('tax');
            Route::post('discount', 'POSController@update_discount')->name('discount');
            Route::get('customers', 'POSController@get_customers')->name('customers');
            Route::post('order', 'POSController@place_order')->name('order');
            Route::get('orders', 'POSController@order_list')->name('orders');
            Route::get('order-details/{id}', 'POSController@order_details')->name('order-details');
            Route::get('invoice/{id}', 'POSController@generate_invoice');
            Route::get('clear', 'POSController@clear_session_data')->name('clear');
            Route::post('customer-store', 'POSController@customer_store')->name('customer-store');
            Route::any('store-keys', 'POSController@store_keys')->name('store-keys');
            Route::post('session-destroy', 'POSController@session_destroy')->name('session-destroy');
            Route::post('add-delivery-address', 'POSController@addDeliveryInfo')->name('add-delivery-address');
            Route::get('get-distance', 'POSController@get_distance')->name('get-distance');
            Route::post('order_type/store', 'POSController@order_type_store')->name('order_type.store');

        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::post('increase-preparation-time/{id}', 'OrderController@preparation_time')->name('increase-preparation-time');
            Route::get('status', 'OrderController@status')->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
            Route::get('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::get('export-excel', 'OrderController@export_excel')->name('export-excel');
            Route::get('ajax-change-delivery-time-date/{order_id}', 'OrderController@ajax_change_delivery_time_date')->name('ajax-change-delivery-time-date');
        });

        Route::group(['prefix' => 'table/order', 'as' => 'table.order.', 'middleware' => ['app_activate:' . APPS['table_app']['software_id']]], function () {
            Route::get('list/{status}', 'TableOrderController@order_list')->name('list');
            Route::get('details/{id}', 'TableOrderController@order_details')->name('details');
            Route::get('running', 'TableOrderController@table_running_order')->name('running');
            Route::get('running/invoice', 'TableOrderController@running_order_invoice')->name('running.invoice');
            Route::get('export-excel', 'TableOrderController@export_excel')->name('export-excel');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{id}', 'OrderController@update_shipping')->name('update-shipping');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');
            Route::post('search', 'OrderController@search')->name('search');
        });

        Route::group(['prefix' => 'table', 'as' => 'table.','middleware'=>[ 'app_activate:' . APPS['table_app']['software_id']]], function () {
            Route::get('list', 'TableController@list')->name('list');
            Route::post('store', 'TableController@store')->name('store');
            Route::get('edit/{id}', 'TableController@edit')->name('edit');
            Route::post('update/{id}', 'TableController@update')->name('update');
            Route::delete('delete/{id}', 'TableController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'TableController@status')->name('status');
            Route::get('index', 'TableController@index')->name('index');
        });

        Route::group(['prefix' => 'kitchen', 'as' => 'kitchen.','middleware'=>[ 'app_activate:' . APPS['kitchen_app']['software_id']]], function () {
            Route::get('list', 'KitchenController@list')->name('list');
            Route::get('add-new', 'KitchenController@add_new')->name('add-new');
            Route::post('add-new', 'KitchenController@store');
            Route::get('edit/{id}', 'KitchenController@edit')->name('edit');
            Route::post('update/{id}', 'KitchenController@update')->name('update');
            Route::delete('delete/{id}', 'KitchenController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'KitchenController@status')->name('status');
        });

        Route::group(['prefix' => 'promotion', 'as' => 'promotion.','middleware'=>[ 'app_activate:' . APPS['table_app']['software_id']]], function () {
            Route::get('create', 'BranchPromotionController@create')->name('create');
            Route::post('store', 'BranchPromotionController@store')->name('store');
            Route::get('edit/{id}', 'BranchPromotionController@edit')->name('edit');
            Route::post('update/{id}', 'BranchPromotionController@update')->name('update');
            Route::delete('delete/{id}', 'BranchPromotionController@delete')->name('delete');
            Route::get('branch/{id}', 'BranchPromotionController@branch_wise_list')->name('branch');
            Route::get('status/{id}/{status}', 'BranchPromotionController@status')->name('status');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('list', 'ProductController@list')->name('list');
            Route::get('set-price/{id}', 'ProductController@set_price_index')->name('set-price');
            Route::post('set-price-update/{id}', 'ProductController@set_price_update')->name('set-price-update');
            Route::get('status/{id}/{status}', 'ProductController@status')->name('status');
        });
    });
});


