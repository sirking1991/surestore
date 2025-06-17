<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryAdjustmentFactory> */
    use HasFactory, SoftDeletes;
    
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
        'adjustment_date' => 'date',
        'approved_at' => 'datetime',
    ];
    
    /**
     * Get the storage location that owns the adjustment.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class);
    }
    
    /**
     * Get the user who created the adjustment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user who approved the adjustment.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the items for the adjustment.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryAdjustmentItem::class);
    }
}
