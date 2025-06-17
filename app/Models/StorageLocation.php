<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageLocation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
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
        'capacity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the storage that owns this location.
     */
    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    /**
     * Get the products stored at this location.
     */
    /**
     * Get the products for the storage location.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the full location code (including storage code)
     */
    public function getFullCodeAttribute()
    {
        return $this->storage->code . '-' . $this->code;
    }
    
    /**
     * Get the full location path
     */
    public function getLocationPathAttribute()
    {
        $parts = [];
        
        if ($this->zone) $parts[] = $this->zone;
        if ($this->aisle) $parts[] = $this->aisle;
        if ($this->rack) $parts[] = $this->rack;
        if ($this->shelf) $parts[] = $this->shelf;
        if ($this->bin) $parts[] = $this->bin;
        
        return implode('-', $parts);
    }
}
