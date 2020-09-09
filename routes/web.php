<?php

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', 'ProductController@index');


/* Products routes */

Route::get('products', 'ProductController@index')->name('products.index');
Route::get('products/{slug}', 'ProductController@show')->name('products.show');
Route::get('/search', 'ProductController@search')->name('products.search');

/* Cart routes */

Route::group(['middleware' => 'auth'], function() {
    Route::get('cart', 'CartController@index')->name('cart.index');
    Route::post('cart/add', 'CartController@store')->name('cart.store');
    Route::put('cart/{rowid}', 'CartController@update')->name('cart.update');
    Route::delete('cart/{rowid}', 'CartController@destroy')->name('cart.destroy');
    Route::get('emptycart', function() {
        Cart::destroy();
    });
});

/* Checkout routes */

Route::group(['middleware' => 'auth'], function() {
    Route::get('checkout', 'CheckoutController@index')->name('checkout.index');
    Route::post('checkout', 'CheckoutController@store')->name('checkout.store');
    Route::get('/thanks', 'CheckoutController@thanks')->name('checkout.thanks');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
