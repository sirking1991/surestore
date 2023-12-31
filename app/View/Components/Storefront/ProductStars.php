<?php

namespace App\View\Components\Storefront;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductStars extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public $rating)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
            <div>
            <i @class(["fa fa-star", "text-warning"=>$rating>=1, "text-muted"=>$rating<1])></i>
            <i @class(["fa fa-star", "text-warning"=>$rating>=2, "text-muted"=>$rating<2])></i>
            <i @class(["fa fa-star", "text-warning"=>$rating>=3, "text-muted"=>$rating<3])></i>
            <i @class(["fa fa-star", "text-warning"=>$rating>=4, "text-muted"=>$rating<4])></i>
            <i @class(["fa fa-star", "text-warning"=>$rating>=5, "text-muted"=>$rating<5])></i>
            </div>
            blade;
    }
}
