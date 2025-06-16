<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDelivery extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseDeliveryFactory> */
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
        'delivery_date' => 'date',
        'expected_delivery_date' => 'date',
        'shipping_cost' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'total_volume' => 'decimal:2',
    ];
    
    /**
     * Get the supplier that owns the purchase delivery.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Get the purchase order associated with the purchase delivery.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
    /**
     * Get the items for the purchase delivery.
     */
    public function items()
    {
        return $this->hasMany(PurchaseDeliveryItem::class);
    }
    
    /**
     * Get the purchase invoices for the purchase delivery.
     */
    public function invoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
