<footer class="bg-dark" id="tempaltemo_footer">
    <div class="container">
        <div class="row">

            <div class="col-md-4 pt-5">
                <h2 class="h2 text-success border-bottom pb-3 border-light logo">{{$store->name}}</h2>
                <ul class="list-unstyled text-light footer-link-list">
                    <li>
                        <i class="fas fa-map-marker-alt fa-fw"></i>
                        {{$store->completeAddress()}}
                    </li>
                    <li>
                        <i class="fa fa-phone fa-fw"></i>
                        <a class="text-decoration-none" href="tel:{{$store->phone}}">{{$store->phone}}</a>
                    </li>
                    <li>
                        <i class="fa fa-envelope fa-fw"></i>
                        <a class="text-decoration-none" href="mailto:{{$store->email}}">{{$store->email}}</a>
                    </li>
                </ul>
            </div>

            <div class="col-md-4 pt-5">
                <h2 class="h2 text-light border-bottom pb-3 border-light">Products</h2>
                <ul class="list-unstyled text-light footer-link-list">
                    <li><a class="text-decoration-none" href="#">Luxury</a></li>
                    <li><a class="text-decoration-none" href="#">Sport Wear</a></li>
                    <li><a class="text-decoration-none" href="#">Men's Shoes</a></li>
                    <li><a class="text-decoration-none" href="#">Women's Shoes</a></li>
                    <li><a class="text-decoration-none" href="#">Popular Dress</a></li>
                    <li><a class="text-decoration-none" href="#">Gym Accessories</a></li>
                    <li><a class="text-decoration-none" href="#">Sport Shoes</a></li>
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
                            "d-none"=>empty($store->facebook)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$store->facebook}}"><i
                                class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($store->instagram)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$store->instagram}}"><i
                                class="fab fa-instagram fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($store->twitter)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$store->twitter}}"><i
                                class="fab fa-twitter fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($store->linkedin)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$store->linkedin}}"><i
                                class="fab fa-linkedin fa-lg fa-fw"></i></a>
                    </li>
                    <li @class([
                            "list-inline-item",
                            "border border-light",
                            "rounded-circle text-center", 
                            "d-none"=>empty($store->tiktok)])>
                        <a class="text-light text-decoration-none" target="_blank" href="{{$store->tiktok}}"><i
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
                        Copyright &copy; {{date('Y')}} {{$store->name}}
                        | Powered by <a rel="sponsored" href="https://surestore.com" target="_blank">SureStore</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</footer>