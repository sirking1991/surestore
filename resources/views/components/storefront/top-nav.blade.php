<nav class="navbar navbar-expand-lg bg-dark navbar-light d-none d-lg-block" id="templatemo_nav_top">
    <div class="container text-light">
        <div class="w-100 d-flex justify-content-between">
            <div>
                <i class="fa fa-envelope mx-2"></i>
                <a class="navbar-sm-brand text-light text-decoration-none"
                    href="mailto:{{$storeFront->email}}">{{$storeFront->email}}</a>
                <i class="fa fa-phone mx-2"></i>
                <a class="navbar-sm-brand text-light text-decoration-none" href="tel:{{$storeFront->phone}}">{{$storeFront->phone}}</a>
            </div>
            <div>
                <a @class(["text-light", "d-none"=>empty($storeFront->facebook)]) href="{{$storeFront->facebook}}" target="_blank" rel="sponsored"><i
                        class="fab fa-facebook-f fa-sm fa-fw me-2"></i></a>
                <a @class(["text-light", "d-none"=>empty($storeFront->instagram)]) href="{{$storeFront->instagram}}" target="_blank"><i
                        class="fab fa-instagram fa-sm fa-fw me-2"></i></a>
                <a @class(["text-light", "d-none"=>empty($storeFront->twitter)]) href="{{$storeFront->twitter}}" target="_blank"><i
                        class="fab fa-twitter fa-sm fa-fw me-2"></i></a>
                <a @class(["text-light", "d-none"=>empty($storeFront->linkedin)]) href="{{$storeFront->linkedin}}" target="_blank"><i
                        class="fab fa-linkedin fa-sm fa-fw me-2"></i></a>
                <a @class(["text-light", "d-none"=>empty($storeFront->tiktok)]) href="{{$storeFront->tiktok}}" target="_blank">
                        <i class="fab fa-tiktok fa-sm fa-fw"></i></a>
            </div>
        </div>
    </div>
</nav>