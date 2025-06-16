<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disbursement extends Model
{
    /** @use HasFactory<\Database\Factories\DisbursementFactory> */
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
        'disbursement_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    /**
     * Get the supplier that owns the disbursement.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Get the purchase invoice associated with the disbursement.
     */
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
