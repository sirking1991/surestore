<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreFront;
use Illuminate\Http\Request;

class StoreFrontController extends Controller
{
    function index(Store $store, Request $request) 
    {
        return view('storefront.index',[
            'store' => $store
        ]);
    }

    function about(Store $store, Request $request)
    {
        return view('storefront.about', [
            'store' => $store,
            'storeFront' => $store->activeStorefront(),
        ]);
    }

    function shop(Store $store, Request $request)
    {
        $products = $store->products()->paginate(9)->withQueryString();

        return view('storefront.shop', [
            'store' => $store,
            'products' => $products
        ]);
    }    
}
