<?php

namespace App\View\Components\Storefront;

use App\Models\Store;
use App\Models\StoreFront;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public $storeFront;
    /**
     * Create a new component instance.
     */
    public function __construct(public Store $store) {
        $this->storeFront = $store->activeStorefront();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.storefront.footer');
    }
}
