<?php

use App\Http\Controllers\StoreFrontController;
use App\Models\Store;
use Illuminate\Support\Facades\Route;

$domain = env('APP_DOMAIN');

Route::domain('{store:slug}.' . $domain)
    ->group(function () {

        // store frontpage
        Route::get('/', [StoreFrontController::class, 'index']);
        Route::get('/about', [StoreFrontController::class, 'about']);
        Route::get('/shop', [StoreFrontController::class, 'shop']);
        Route::get('/contact-us', [StoreFrontController::class, 'contactUs']);


    }
);


Route::get('/', function () {
    return view('welcome');
});

