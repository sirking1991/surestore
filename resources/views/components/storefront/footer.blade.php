<footer class="bg-dark" id="tempaltemo_footer">
    <div class="container">
        <div class="row">

            <div class="col-md-4 pt-5">
                <h2 class="h2 text-success border-bottom pb-3 border-light logo">{{$storeFront->name}}</h2>
                <ul class="list-unstyled text-light footer-link-list">
                    <li>
                        <i class="fas fa-map-marker-alt fa-fw"></i>
                        {{$storeFront->completeAddress()}}
                    </li>
                    <li>
                        <i class="fa fa-phone fa-fw"></i>
                        <a class="text-decoration-none" href="tel:{{$storeFront->phone}}">{{$storeFront->phone}}</a>
                    </li>
                    <li>
                        <i class="fa fa-envelope fa-fw"></i>
                        <a class="text-decoration-none" href="mailto:{{$storeFront->email}}">{{$storeFront->email}}</a>
                    </li>
                </ul>
            </div>

            <div @class(["col-md-4 pt-5", "d-none"=>!$showCategories])>
                <h2 class="h2 text-light border-bottom pb-3 border-light">Products</h2>
                <ul class="list-unstyled text-light footer-link-list">
                    @foreach ($store->categories as $category)
                        <li><a class="text-decoration-none" href="#">{{ $category->name }}</a></li>    
                    @endforeach
                </ul>
            </div>

            <div class="col-md-4 pt-5">
                <h2 class="h2 text-light border-bottom pb-3 border-light">Further Info</h2>
                <ul class="list-unstyled text-light footer-link-list">
                    <li><a class="text-decoration-none" href="/">Home</a></li>
                    <li><a class="text-decoration-none" href="/about">About Us</a></li>
                    <li><a class="text-decoration-none" href="/shop">Shop</a></li>
                    <li><a class="text-decoration-none" href="/faq">FAQs</a></li>
                    <li><a class="text-decoration-none" href="/contact-us">Contact</a></li>
                </ul>
            </div>

        </div>

        <div class="row text-light mb-4">
            <div class="col-12 mb-3">
                <div class="w-100 my-3 border-top border-light"></div>
            </div>
            <div class="col-auto me-auto">
                <ul class="list-inline text-left footer-icons">
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($storeFront->facebook)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$storeFront->facebook}}"><i
                                class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($storeFront->instagram)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$storeFront->instagram}}"><i
                                class="fab fa-instagram fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($storeFront->twitter)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$storeFront->twitter}}"><i
                                class="fab fa-twitter fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($storeFront->linkedin)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$storeFront->linkedin}}"><i
                                class="fab fa-linkedin fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($storeFront->tiktok)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$storeFront->tiktok}}"><i
                                class="fab fa-tiktok fa-lg fa-fw"></i></a>
                    </li>
                </ul>
            </div>
            <div class="col-auto">
                <label class="sr-only" for="subscribeEmail">Email address</label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control bg-dark border-light" id="subscribeEmail"
                        placeholder="Email address">
                    <div class="input-group-text btn-success text-light">Subscribe</div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 bg-black py-3">
        <div class="container">
            <div class="row pt-2">
                <div class="col-12">
                    <p class="text-left text-light">
                        Copyright &copy; {{date('Y')}} {{$storeFront->name}}
                        | Powered by <a rel="sponsored" href="https://surestore.com" target="_blank">SureStore</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</footer>