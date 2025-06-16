<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionFactory> */
    use HasFactory;
    
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
        'production_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'labor_minutes' => 'integer',
        'setup_minutes' => 'integer',
    ];
    
    /**
     * Get the user who created this production record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the raw materials used in this production.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ProductionMaterial::class);
    }
    
    /**
     * Get the products yielded from this production.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductionProduct::class);
    }
    
    /**
     * Calculate the total production time in minutes.
     */
    public function getTotalTimeAttribute(): ?int
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInMinutes($this->end_time);
        }
        
        return null;
    }
    
    /**
     * Calculate the efficiency ratio (output value / input cost).
     */
    public function getEfficiencyRatioAttribute(): ?float
    {
        $inputCost = $this->materials()->sum('total_cost');
        $outputValue = $this->products()->sum('total_cost');
        
        if ($inputCost > 0) {
            return $outputValue / $inputCost;
        }
        
        return null;
    }
    
    /**
     * Get the work orders associated with this production.
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}
