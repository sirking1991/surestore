<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreFrontController extends Controller
{
    function index(Store $store, Request $request) {
        $store->load('products');
        return view('storefront.index',[
            'store' => $store,
        ]);
    }

    function about(Store $store, Request $request)
    {
        return view('storefront.about', [
            'store' => $store,
        ]);
    }
}
