<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreFront extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'meta_about' => 'json'
    ];

    public function completeAddress(): string
    {
        return $this->street 
            . ' ' . $this->city 
            . ' ' . $this->state 
            . ' ' . $this->country 
            . ' ' . $this->postal_code;
    }

}
