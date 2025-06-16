<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceItem extends Model
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
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    /**
     * Get the purchase invoice that owns the purchase invoice item.
     */
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get the product associated with the purchase invoice item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the purchase order item associated with the purchase invoice item.
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Get the purchase delivery item associated with the purchase invoice item.
     */
    public function purchaseDeliveryItem()
    {
        return $this->belongsTo(PurchaseDeliveryItem::class);
    }
}
