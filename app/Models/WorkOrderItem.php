<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderItemFactory> */
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
        'estimated_minutes' => 'integer',
        'actual_minutes' => 'integer',
        'sequence_number' => 'integer',
        'quantity' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    /**
     * Get the work order that owns this item.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
    
    /**
     * Get the user assigned to this work order item.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the product associated with this work order item, if any.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Calculate the completion percentage of the item.
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }
        
        if ($this->status === 'pending') {
            return 0;
        }
        
        if ($this->status === 'in_progress') {
            if ($this->started_at && $this->estimated_minutes) {
                $elapsedMinutes = now()->diffInMinutes($this->started_at);
                $percentage = min(95, (int) ($elapsedMinutes / $this->estimated_minutes * 100));
                return $percentage;
            }
            return 50;
        }
        
        return 0;
    }
    
    /**
     * Check if the work order item is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        
        if (!$this->workOrder || !$this->workOrder->scheduled_date) {
            return false;
        }
        
        return $this->workOrder->scheduled_date < now()->startOfDay();
    }
    
    /**
     * Get the duration in minutes.
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        
        return null;
    }
}
