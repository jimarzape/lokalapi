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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['namespace' => 'Api'], function(){
	Route::post('login','AuthController@login');
	Route::post('register','RegisterController@index');
	Route::get('auth/email-checker','AuthController@email_checker');
	Route::post('auth/logout','AuthController@logout');
	Route::get('auth/session-checker','AuthController@session_checker');

	Route::get('item/search','ItemController@search');
	Route::get('item/new-arrivals','ItemController@new_arrivals');
	Route::get('item/random-list','ItemController@list_random');
	Route::post('item/variant','ItemController@variant');

	Route::post('order/rate','ItemController@rate');
	
	Route::get('ads','AdsController@index');
	Route::get('delivery/checker','AddressList@index');
	Route::get('delivery/barangay','AddressList@barangay');

	Route::get('brands/items','ItemController@item_brand');
	Route::get('brands','BrandController@index');

	Route::get('cart', 'CartController@index');
	Route::post('cart/remove-item', 'CartController@remove');
	Route::post('cart/update/qty', 'CartController@update_qty');
	Route::get('cart/add', 'CartController@add');
	Route::get('cart/weight', 'CartController@weight');

	Route::get('address-list', 'AddressList@index');
	Route::get('address-list/barangay', 'AddressList@barangay');
	Route::get('address-list/municipality', 'AddressList@municipality');
	Route::get('address-list/province', 'AddressList@province');

	Route::get('order/gen', 'OrderController@order_no_gen');
	Route::get('order/cancel/gen', 'OrderController@cancel_order_gen_no');
	Route::get('order/status', 'OrderController@status');
	Route::post('order/cancel', 'OrderController@cancel_order');
	Route::get('order/cancel/list', 'OrderController@cancel_list');
	Route::post('order/checkout', 'OrderController@checkout');
	Route::get('order/order_status/{status}', 'OrderController@order_status');

	Route::post('fee/mrspeedy', 'DeliveryFeeChecker@mr_speedy');
	Route::post('fee/ninjavan', 'DeliveryFeeChecker@ninjavan');

	Route::post('followers', 'FollowerController@index');
	Route::post('followers/follow', 'FollowerController@follow');
	Route::post('followers/check', 'FollowerController@check');
});