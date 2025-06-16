<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model
{
    /** @use HasFactory<\Database\Factories\StorageFactory> */
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
        'is_main' => 'boolean',
    ];
    
    /**
     * Get the locations within this storage.
     */
    public function locations()
    {
        return $this->hasMany(StorageLocation::class);
    }
    
    /**
     * Get the delivery items stored at this storage.
     */
    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItem::class);
    }
    
    /**
     * Get the purchase delivery items stored at this storage.
     */
    public function purchaseDeliveryItems()
    {
        return $this->hasMany(PurchaseDeliveryItem::class);
    }
    
    /**
     * Get the products associated with this storage.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'delivery_items')
            ->union($this->belongsToMany(Product::class, 'purchase_delivery_items'));
    }
}
