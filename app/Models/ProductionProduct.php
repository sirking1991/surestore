<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionProduct extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionProductFactory> */
    use HasFactory;
    
    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];
    
    /**
     * Get the production that yielded this product.
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
    
    /**
     * Get the product that was produced.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the storage where this product is stored.
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
    
    /**
     * Get the specific storage location where this product is stored.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }
}
