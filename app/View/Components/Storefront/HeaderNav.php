<?php

namespace App\View\Components\storefront;

use App\Models\Store;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderNav extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Store $store)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.storefront.header-nav');
    }
}