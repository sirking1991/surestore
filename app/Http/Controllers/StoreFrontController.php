<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreFront;
use Illuminate\Http\Request;

class StoreFrontController extends Controller
{
    function index(Store $store, Request $request) {
        $storeFront = StoreFront::where('store_id', $store->id)
            ->where('status', 'active')
            ->first();
            
        return view('storefront.index',[
            'store' => $store,
            'storeFront' => $storeFront,
        ]);
    }

    function about(Store $store, Request $request)
    {
        return view('storefront.about', [
            'store' => $store,
        ]);
    }
}
