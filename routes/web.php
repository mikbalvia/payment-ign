<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Illuminate\Http\Request;
use App\User;

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
        return redirect('/home');
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
Route::get('/checkout/finish/{id}/{channel}', 'CheckoutController@finish')->name('checkout-finish');
Route::get('checkout/nc-finish',  'CheckoutController@callbackNicepay');
Route::get('/checkout/qrcode', function () { return view('checkout.qrcode');});
Route::get('/checkout/step2/{id}', function ($id = null, Request $request) {

    //session cart
    $cart= $request->session()->get('cart');

    //check wheter product and user is exist
    $product = ($id) ? Product::where('code', $id)->with('additionalProduct')->get() : "";
    if ($request->cookie('piu')) {
        $user = User::find($request->cookie('piu'));
    } else {
        return redirect('/checkout/step1/' . $product[0]->code);
    }
    //$user = User::find(1);
    return view('checkout.payment-method', compact('product', 'user', 'cart'));
})->name('payment');

Auth::routes();
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('product', 'ProductController');
    Route::resource('additional-product', 'AdditionalProductController');
    Route::get('/additional-product/{id}/create', 'AdditionalProductController@create');
    Route::get('/additional-product/product/{product_id}', 'AdditionalProductController@index');
    

    /**
     * transaction url
     */
    Route::get('/transaction', 'TransactionController@index')->name('transaction');
    Route::get('/transaction/{id}/edit', 'TransactionController@edit')->name('transaction.edit');
    Route::put('/transaction/{id}', 'TransactionController@update')->name('transaction.update');
});

Route::get('/test', function () {
    return 'for test';
});




