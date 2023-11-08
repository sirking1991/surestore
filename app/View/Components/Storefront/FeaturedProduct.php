<?php

namespace App\View\Components\storefront;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreFront;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeaturedProduct extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Store $store, 
        public StoreFront $storeFront
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $products = Product::where('store_id', $this->store->id)
            ->whereIn('id', json_decode($this->storeFront->featured_products))
            ->limit(3)
            ->get();

        return view('components.storefront.featured-product',['products'=>$products]);
    }
}
