<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class ProductOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'options' => AsArrayObject::class,
    ];
}
