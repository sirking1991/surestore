<?php

namespace App\View\Components\storefront;

use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\StoreFront;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MonthCategory extends Component
{
    public $storeFront;
    /**
     * Create a new component instance.
     */
    public function __construct(public Store $store) {
        $this->storeFront = $store->activeFrontstore();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $categories = ProductCategory::where('store_id', $this->store->id)
            ->whereIn('id', json_decode($this->storeFront->month_category))
            ->limit(3)
            ->get();

        return view('components.storefront.month-category', ['categories'=>$categories]);
    }
}
