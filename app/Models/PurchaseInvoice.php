<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    /** @use HasFactory<\Database\Factories\PurchaseInvoiceFactory> */
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
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];
    
    /**
     * Get the supplier that owns the purchase invoice.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Get the purchase order associated with the purchase invoice.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
    /**
     * Get the purchase delivery associated with the purchase invoice.
     */
    public function purchaseDelivery()
    {
        return $this->belongsTo(PurchaseDelivery::class);
    }
    
    /**
     * Get the items for the purchase invoice.
     */
    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
    
    /**
     * Get the disbursements for the purchase invoice.
     */
    public function disbursements()
    {
        return $this->belongsToMany(Disbursement::class, 'disbursement_purchase_invoice')
            ->withPivot('amount', 'notes')
            ->withTimestamps();
    }
}
