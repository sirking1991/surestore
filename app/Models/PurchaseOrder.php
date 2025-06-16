<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseOrderFactory> */
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
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    /**
     * Get the supplier that owns the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Get the items for the purchase order.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
    /**
     * Get the deliveries for the purchase order.
     */
    public function deliveries()
    {
        return $this->hasMany(PurchaseDelivery::class);
    }
    
    /**
     * Get the invoices for the purchase order.
     */
    public function invoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
