<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

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
    if (Auth::check()) {
        return redirect('/product');
    } else {
        return redirect('/checkout/step1/{id}');
    }
});

Route::get('/home', 'HomeController@index')->name('home');

/**
 * payment point url
 */
Route::get('/checkout/step1/{id}', 'CheckoutController@index')->name('checkout');
Route::post('/storeCustomerInfo/{id}', 'CheckoutController@storeCustomerInfo')->name('store-customer');
Route::get('/getPrefixNumber', 'CheckoutController@getPrefixNumber')->name('get-prefix-number');
Route::post('/checkoutProcess', 'CheckoutController@process')->name('checkout-process');
Route::get('/checkout/finish/{id}', 'CheckoutController@finish')->name('checkout-finish');
Route::get('/checkout/step2/{id}', function ($id = null) {

    $product = ($id) ? Product::where('code', $id)->get() : "";

    return view('checkout.payment-method', compact('product'));
})->name('payment');

Auth::routes();
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('product', 'ProductController');
});
