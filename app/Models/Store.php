<?php

namespace App\Models;

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

    public function completeAddress(): string
    {
        return $this->street . ' ' . $this->city . ' ' . $this->state . ' ' . $this->country . ' ' . $this->postal_code;
    }
}
