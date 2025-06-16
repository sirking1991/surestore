<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderFactory> */
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
        'scheduled_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'estimated_minutes' => 'integer',
        'actual_minutes' => 'integer',
    ];
    
    /**
     * Get the production associated with this work order.
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
    
    /**
     * Get the user who created this work order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user assigned to this work order.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Calculate the completion percentage of the work order.
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }
        
        if ($this->status === 'draft') {
            return 0;
        }
        
        if ($this->status === 'scheduled') {
            return 25;
        }
        
        if ($this->status === 'in_progress') {
            if ($this->start_time && $this->estimated_minutes) {
                $elapsedMinutes = now()->diffInMinutes($this->start_time);
                $percentage = min(95, (int) ($elapsedMinutes / $this->estimated_minutes * 100));
                return $percentage;
            }
            return 50;
        }
        
        return 0;
    }
    
    /**
     * Check if the work order is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        
        return $this->scheduled_date < now()->startOfDay();
    }
    
    /**
     * Get the items for this work order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(WorkOrderItem::class);
    }
    
    /**
     * Get the completion percentage based on items.
     */
    public function getItemsCompletionPercentageAttribute(): int
    {
        $items = $this->items;
        
        if ($items->isEmpty()) {
            return $this->completion_percentage;
        }
        
        $totalItems = $items->count();
        $completedItems = $items->where('status', 'completed')->count();
        $inProgressItems = $items->where('status', 'in_progress');
        
        $completedPercentage = ($completedItems / $totalItems) * 100;
        
        // Add partial completion from in-progress items
        $inProgressPercentage = 0;
        if ($inProgressItems->count() > 0) {
            $inProgressPercentage = $inProgressItems->sum('completion_percentage') / $totalItems;
        }
        
        return (int) min(99, $completedPercentage + $inProgressPercentage);
    }
}
