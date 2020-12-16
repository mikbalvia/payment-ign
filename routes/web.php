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

Route::get('/', function () {
    return redirect('/checkout');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * payment point url
 */
Route::get('/checkout', 'CheckoutController@index')->name('checkout');
Route::post('/payment-method', 'CheckoutController@paymentMethod')->name('payment-method');
Route::get('/getPrefixNumber', 'CheckoutController@getPrefixNumber')->name('get-prefix-number');
Route::post('/checkoutProcess', 'CheckoutController@process')->name('checkout-process');
Route::get('/checkoutFinish', 'CheckoutController@finish')->name('checkout-finish');

Route::group(['middleware' => ['auth', 'verified']], function () {
});
