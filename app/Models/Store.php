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

    function products() {
        return $this->hasMany(Product::class);
    }

    function storefront() {
        return $this->hasMany(StoreFront::class);
    }

}
