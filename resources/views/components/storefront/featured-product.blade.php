<section class="bg-light">
    <div class="container py-5">
        <div class="row text-center py-3">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Featured Product</h1>
                <p>
                    Reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                    Excepteur sint occaecat cupidatat non proident.
                </p>
            </div>
        </div>
        <div class="row">
            @foreach ($products as $product)
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <a href="shop-single.html">
                        @php
                            $images = $product->images;
                        @endphp
                        <img src="{{$images[0]}}" class="card-img-top" alt="{{$product->name}}">
                    </a>
                    <div class="card-body">
                        <ul class="list-unstyled d-flex justify-content-between">
                            <li>
                                @for ($i = 1; $i <= 5; $i++)
                                <i @class([
                                    "fa fa-star", 
                                    "text-warning"=>$product->rating >= $i,
                                    "text-muted"=>$product->rating < $i,
                                    ])></i>    
                                @endfor
                            </li>
                            <li class="text-muted text-right">{{number_format($product->price,2)}}</li>
                        </ul>
                        <a href="shop-single.html" class="h2 text-decoration-none text-dark">{{$product->name}}</a>
                        <p class="card-text">{{$product->description}}</p>
                        <p class="text-muted">Reviews (24)</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>