<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStorageMinQuantity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_storage_min_quantity';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'storage_id',
        'storage_location_id',
        'min_quantity',
    ];
    
    protected $appends = ['display_title'];
    
    protected $with = ['storage', 'storageLocation'];
    
    protected $touches = ['product'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_quantity' => 'decimal:2',
    ];

    /**
     * Get the product that owns the minimum quantity record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the storage location that owns the minimum quantity record.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }
    
    /**
     * Get the storage that owns the storage location.
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }
    
    /**
     * Get the display title for the item.
     */
    public function getDisplayTitleAttribute(): string
    {
        // If relationships are loaded, use them directly
        if ($this->relationLoaded('storage') && $this->storage) {
            $storageName = $this->storage->name;
            $locationName = '';
            
            if ($this->relationLoaded('storageLocation') && $this->storageLocation) {
                $locationName = ' - ' . $this->storageLocation->name;
            } elseif ($this->storage_location_id) {
                $locationName = ' - Location #' . $this->storage_location_id;
            }
            
            return $storageName . $locationName;
        }
        
        // Fallback to IDs if relationships aren't loaded
        if ($this->storage_id) {
            $storage = \App\Models\Storage::find($this->storage_id);
            $storageName = $storage ? $storage->name : 'Storage #' . $this->storage_id;
            
            $locationName = '';
            if ($this->storage_location_id) {
                $location = StorageLocation::find($this->storage_location_id);
                $locationName = $location ? ' - ' . $location->name : ' - Location #' . $this->storage_location_id;
            }
            
            return $storageName . $locationName;
        }
        
        return 'New Storage Rule';
    }
}
