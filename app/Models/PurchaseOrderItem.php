<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
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
        'quantity_invoiced' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'expected_delivery_date' => 'date',
    ];

    /**
     * Get the purchase order that owns the purchase order item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product associated with the purchase order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the purchase delivery items for the purchase order item.
     */
    public function deliveryItems()
    {
        return $this->hasMany(PurchaseDeliveryItem::class);
    }

    /**
     * Get the purchase invoice items for the purchase order item.
     */
    public function invoiceItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
