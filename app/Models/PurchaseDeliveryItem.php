<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDeliveryItem extends Model
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
     * Get the purchase delivery that owns the purchase delivery item.
     */
    public function purchaseDelivery()
    {
        return $this->belongsTo(PurchaseDelivery::class);
    }

    /**
     * Get the product associated with the purchase delivery item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the purchase order item associated with the purchase delivery item.
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Get the purchase invoice items for the purchase delivery item.
     */
    public function invoiceItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
