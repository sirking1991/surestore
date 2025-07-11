<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    
    /**
     * Get the supplier that owns the product.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Get the storage location that owns the product.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }
    
    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
    
    /**
     * Get the minimum quantities for this product across different storage locations.
     */
    public function storageMinQuantities(): HasMany
    {
        return $this->hasMany(ProductStorageMinQuantity::class);
    }
}
