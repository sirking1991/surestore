<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreFront;
use App\Models\Product;
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

    function product(Store $store, Product $product)
    {
        $product->load(['category','options']);
        return view('storefront.product', [
            'store' => $store,
            'product'=>$product
        ]);
    }

    function addToCart(Store $store, Request $request) {
        $cart = json_decode(session('cart', ''));
        if (!$cart) $cart = ['items'=>[], 'discount'=>0];

        $product = Product::with('options')
            ->where('store_id', $store->id)
            ->where('id', $request->product_id)
            ->first();

        if (!$product) {
            return back()->with('error', 'An error occured while adding product to cart');
        }

        $cart['items'][] = [
            'product' => ['id'=>$request->product_id, 'name'=>$product->name, 'sku'=>$product->sku],            
            'options' => [],
            'discount' => 0,
            'price' => $product->price,
            'qty' => $request->qty,            
        ];

        session(['cart'=>json_encode($cart)]);

        return back();
    }
}
