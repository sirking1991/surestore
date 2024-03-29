<?php

use App\Http\Controllers\StoreFrontController;
use App\Models\Store;
use Illuminate\Support\Facades\Route;

$domain = env('APP_DOMAIN');

Route::domain('{store:slug}.' . $domain)
    ->group(function () {

        // store frontpage
        Route::get('/', [StoreFrontController::class, 'index'])->name('storefront');
        Route::get('/about', [StoreFrontController::class, 'about'])->name('storefront.about');
        Route::get('/shop', [StoreFrontController::class, 'shop'])->name('storefront.shop');
        Route::get('/product/{product:slug}', [StoreFrontController::class, 'product'])->name('storefront.product');
        Route::post('/add-to-cart', [StoreFrontController::class, 'addToCart'])->name('storefront.add-to-cart');
        Route::get('/contact-us', [StoreFrontController::class, 'contactUs'])->name('storefront.contactus');


    }
);


Route::get('/', function () {
    return view('welcome');
});

