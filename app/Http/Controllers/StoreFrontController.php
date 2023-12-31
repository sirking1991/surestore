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
        $category = null;
        if($request->{'category-slug'}){
            $category = $store
                ->categories->where('slug', $request->{'category-slug'})
                ->first();
        }

        $products = $store
            ->products()
            ->when($category, fn($q) => $q->where('category_id', $category->id))
            ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->simplePaginate(9)
            ->withQueryString();
        return view('storefront.shop', [
            'store' => $store,
            'products' => $products
        ]);
    }    
}
