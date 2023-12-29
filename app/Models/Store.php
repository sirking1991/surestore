<?php

namespace App\Models;

use App\Models\Product;
use App\Models\StoreFront;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $activeStorefront;

    function categories() {
        return $this->hasMany(ProductCategory::class);
    }

    function products() {
        return $this->hasMany(Product::class);
    }

    function storefronts() {
        return $this->hasMany(StoreFront::class);
    }

    function activeStorefront() {
        if (! $this->activeStorefront){
            $this->activeStorefront = $this->storefronts()->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
        }
        return $this->activeStorefront;
    }

}
