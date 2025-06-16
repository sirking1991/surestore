<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryItem extends Model
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
        'quantity' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'weight' => 'decimal:2',
        'volume' => 'decimal:2',
    ];

    /**
     * Get the delivery that owns the delivery item.
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the product associated with the delivery item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order item associated with the delivery item.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
    
    /**
     * Get the storage associated with this delivery item.
     */
    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }
    
    /**
     * Get the storage location associated with this delivery item.
     */
    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }
}
