@extends('storefront.layout')

@section('content')

    <x-storefront.top-nav :store="$store"/>

    <x-storefront.header-nav :store="$store"/>

    <section class="bg-success py-5">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-md-8 text-white">
                    <h1>About Us</h1>
                    <p>
                        {!! $storeFront->meta_about['text'] !!}
                    </p>
                </div>
                <div class="col-md-4">
                    <img src="{{ $storeFront->meta_about['image'] }}" alt="About Hero">
                </div>
            </div>
        </div>
    </section>
    
    @php $ourServices = $storeFront->meta_about['our_services']; @endphp            
    <section class="container py-5">
        <div class="row text-center pt-5 pb-3">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Our Services</h1>
                <p>{!! $ourServices['text'] !!} </p>
            </div>
        </div>
        <div class="row">            
            @foreach ($ourServices['services'] as $service)
            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-5 services-icon-wap shadow">
                    <div class="h1 text-success text-center"><i class="fa {{ $service['icon'] }}"></i></div>
                    <h2 class="h5 mt-4 text-center">{{ $service['text'] }}</h2>
                </div>
            </div>                
            @endforeach
        </div>
    </section>
    
    @php $ourBrands = $storeFront->meta_about['our_brands']; @endphp            
    <section class="bg-light py-5">
        <div class="container my-4">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Our Brands</h1>
                    <p>{!! $ourBrands['text'] !!}</p>
                </div>
                <div class="col-lg-9 m-auto tempaltemo-carousel">
                    <div class="row d-flex flex-row">
                        <!--Controls-->
                        <div class="col-1 align-self-center">
                            <a class="h1" href="#templatemo-slide-brand" role="button" data-bs-slide="prev">
                                <i class="text-light fas fa-chevron-left"></i>
                            </a>
                        </div>
                        <!--End Controls-->
    
                        <!--Carousel Wrapper-->
                        <div class="col">
                            <div class="carousel slide carousel-multi-item pt-2 pt-md-0" id="templatemo-slide-brand" data-bs-ride="carousel">
                                <!--Slides-->
                                <div class="carousel-inner product-links-wap" role="listbox">
    
                                    <!--First slide-->
                                    <div class="carousel-item active">
                                        <div class="row">
                                            @foreach ($ourBrands['images'] as $brandImg)
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img" src="{{ $brandImg }}" alt="Brand Logo"></a>
                                            </div>                                                
                                            @endforeach
                                        </div>
                                    </div>
                                    <!--End First slide-->
    
                                </div>
                                <!--End Slides-->
                            </div>
                        </div>
                        <!--End Carousel Wrapper-->
    
                        <!--Controls-->
                        <div class="col-1 align-self-center">
                            <a class="h1" href="#templatemo-slide-brand" role="button" data-bs-slide="next">
                                <i class="text-light fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <!--End Controls-->
                    </div>
                </div>
            </div>
        </div>
    </section>



    <x-storefront.footer :store="$store" />

@endsection