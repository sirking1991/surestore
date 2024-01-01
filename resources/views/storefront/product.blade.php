@extends('storefront.layout')

@section('content')

    <x-storefront.search-modal />

    <x-storefront.top-nav :store="$store"/>

    <x-storefront.header-nav :store="$store"/>

    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-5 mt-5">
                <div class="card mb-3">
                    <img class="card-img img-fluid" src="{{$product->images[0]}}" alt="Card image cap" id="product-detail">
                </div>
                <div class="row">
                    <!--Start Controls-->
                    <div class="col-1 align-self-center">
                        <a href="#multi-item-example" role="button" data-bs-slide="prev">
                            <i class="text-dark fas fa-chevron-left"></i>
                            <span class="sr-only">Previous</span>
                        </a>
                    </div>
                    <!--End Controls-->
                    <!--Start Carousel Wrapper-->
                    <div id="multi-item-example" class="col-10 carousel slide carousel-multi-item" data-bs-ride="carousel">
                        <!--Start Slides-->
                        <div class="carousel-inner product-links-wap" role="listbox">
                            
                            @foreach (array_chunk((array)$product->images, 3) as $key => $imgChunk)
                            <!-- slide {{$key+1}}-->
                            <div @class(["carousel-item", "active"=>$key!=1])>
                                <div class="row">
                                    @foreach ($imgChunk as $img)
                                    <div class="col-4">
                                        <a href="#">
                                            <img class="card-img img-fluid" src="{{$img}}" alt="{{ $product->name }}">
                                        </a>
                                    </div>                                        
                                    @endforeach
                                </div>
                            </div>
                            <!--/. slide {{$key+1}}-->
                            @endforeach
                        </div>
                        <!--End Slides-->
                    </div>
                    <!--End Carousel Wrapper-->
                    <!--Start Controls-->
                    <div class="col-1 align-self-center">
                        <a href="#multi-item-example" role="button" data-bs-slide="next">
                            <i class="text-dark fas fa-chevron-right"></i>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    <!--End Controls-->
                </div>
            </div>
            <!-- col end -->
            <div class="col-lg-7 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h2">{{ $product->name }}</h1>
                        <p class="h3 py-2">{{ number_format($product->price,2) }}</p>
                        
                        <section @class(["mt-2", "d-none"=>$product->rating==0])>
                            <x-storefront.product-stars :rating="$product->rating" />
                            <span class="list-inline-item text-dark">
                                Rating {{$product->rating}}
                            </span>
                        </section>

                        <section @class(["mt-2", "d-none"=>!$product->sku])>
                            <h6>SKU:</h6>
                            <p>{!! $product->sku !!}</p>
                        </section>

                        <section @class(["mt-2", "d-none"=>!$product->description])>
                            <h6>Description:</h6>
                            <p>{!! $product->description !!}</p>
                        </section>

                        <form method="POST" action="/add-to-cart">
                            @csrf
                            <input type="hidden" name="product_id" value="{{$product->id}}">

                        <section @class(["mt-2", "d-none"=>!$product->options])>
                            <div class="accordion" id="accordionOptions">
                                @foreach ($product->options as $key => $option)
                                <div class="accordion-item">
                                  <h2 class="accordion-header">
                                    <button @class(["accordion-button", "collapsed"=>$key!=0]) type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                        aria-expanded="true" aria-controls="collapse{{ $key }}">
                                      {{ Str::title($option->name) }}
                                    </button>
                                  </h2>
                                  <div id="collapse{{ $key }}" @class(["accordion-collapse collapse", "show"=>$key==0]) data-bs-parent="#accordionOptions">
                                    <div class="accordion-body">
                                        @foreach ((array)$option->options as $optionItem)
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input" 
                                                type="radio" 
                                                name="{{ Str::slug($option->name) }}" 
                                                value="{{ $optionItem['value'] }}"
                                                id="{{ $optionItem['value'] }}"
                                                />
                                            <label class="form-check-label" for="{{ $optionItem['value'] }}">
                                                {{ Str::title($optionItem['value']) }}
                                                <span @class(['text-muted', 'd-none'=>$optionItem['addon_price']==0])>+ {{ number_format($optionItem['addon_price'],2) }}</span>
                                            </label>
                                        </div>                                            
                                        @endforeach
                                    </div>
                                  </div>
                                </div>
                                @endforeach
                              </div>
                        </section>

                        <section @class(["mt-3"])>
                            <div class="row">
                                <div class="col-md-4">
                                    Price: <span class="price">{{ number_format($product->price,2) }}</span>
                                </div>
                                <div class="col-4">
                                    <ul class="list-inline pb-3">
                                        <li class="list-inline-item text-right">
                                            Quantity
                                            <input type="hidden" name="qty" id="qty" value="1">
                                        </li>
                                        <li class="list-inline-item"><span class="btn btn-success" id="btn-minus">-</span></li>
                                        <li class="list-inline-item"><span class="badge bg-secondary" id="var-value">1</span></li>
                                        <li class="list-inline-item"><span class="btn btn-success" id="btn-plus">+</span></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="mt-5 float-end btn btn-success" value="addtocard">
                                        <i class="bi bi-cart-plus"></i> Add To Cart
                                    </button>
                                </div>
                            </div>

                        </section>

                        </form>
                        
                        @if (session('error'))
                            <section @class(["mt-3"])>
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-storefront.footer :store="$store" :showCategories=false />

@endsection