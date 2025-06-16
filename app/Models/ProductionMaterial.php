<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionMaterialFactory> */
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
     * Get the production that this material is used in.
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
    
    /**
     * Get the product that is used as raw material.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the storage where this material is taken from.
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
    
    /**
     * Get the specific storage location where this material is taken from.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }
}
