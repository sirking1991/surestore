@extends('storefront.layout')

@section('content')
    <x-storefront.search-modal />

    <x-storefront.top-nav :store="$store"/>

    <x-storefront.header-nav :store="$store"/>

    <div class="container py-5">
        <div class="row">

            <div class="col-lg-3">
                <h1 class="h2 pb-4">Categories</h1>
                <ul class="list-unstyled templatemo-accordion">
                    @foreach ($store->categories as $category)
                    <a class="collapsed d-flex justify-content-between h3 text-decoration-none" 
                        href="{{ url('shop'.'?category-slug=' . $category->slug) }}">
                        <li class="pb-3">{{ $category->name }}</li>                        
                    </a>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-md-6 pb-3">
                        <form action="{{ url('shop') }}">
                            <input 
                                name="search" 
                                type="text" 
                                class="form-control" 
                                placeholder="Search product"
                                value="{{ request('search') }}"
                                />
                        </form>
                    </div>
                </div>
                <div class="row">
                    @foreach ($products as $product)
                    <div class="col-md-4">
                        <div class="card mb-4 product-wap rounded-0">
                            <div class="card rounded-0">
                                <img class="card-img rounded-0 img-fluid" src="{{$product->images[0]}}">
                                <div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                    <ul class="list-unstyled d-none">
                                        <li><a class="btn btn-success text-white" href="shop-single.html"><i class="far fa-heart"></i></a></li>
                                        <li><a class="btn btn-success text-white mt-2" href="shop-single.html"><i class="far fa-eye"></i></a></li>
                                        <li><a class="btn btn-success text-white mt-2" href="shop-single.html"><i class="fas fa-cart-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <a href="{{ url('product/' . $product->slug) }}" class="h3 text-decoration-none">{{$product->name}}</a>
                                <ul class="w-100 list-unstyled d-flex justify-content-between mb-0">                                    
                                    <li class="pt-2">
                                        <span class="product-color-dot color-dot-red float-left rounded-circle ml-1"></span>
                                        <span class="product-color-dot color-dot-blue float-left rounded-circle ml-1"></span>
                                        <span class="product-color-dot color-dot-black float-left rounded-circle ml-1"></span>
                                        <span class="product-color-dot color-dot-light float-left rounded-circle ml-1"></span>
                                        <span class="product-color-dot color-dot-green float-left rounded-circle ml-1"></span>
                                    </li>
                                </ul>
                                <ul class="list-unstyled d-flex justify-content-center mb-1">
                                    <li>
                                        <x-storefront.product-stars :rating="$product->rating" />
                                    </li>
                                </ul>
                                <p class="text-center mb-0">{{ number_format($product->price,2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div div="row">
                    {{$products->links()}}
                    <ul class="d-none pagination pagination-lg justify-content-end">
                        <li @class(["page-item"])>
                            <a class="page-link active rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="#"><< Previous</a>
                        </li>
                        <li @class(["page-item"])>
                            <a class="page-link active rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="#">Next >></a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <x-storefront.footer :store="$store" :showCategories=false />

@endsection