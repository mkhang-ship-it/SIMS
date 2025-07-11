<?php
// EventPlanBudgetItem Model - Budget Cost Management for Event Plans
// Created: Budget cost management system linked to event_plans table
// Includes: content, quantity (integer), unit, unit_price (decimal), responsible_person, notes
// Updated: Removed total_amount field, quantity is integer, unit_price is decimal

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlanBudgetItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_plan_id',
        'content',
        'quantity',
        'unit',
        'unit_price',
        'responsible_person',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event plan that owns this budget item
     */
    public function eventPlan(): BelongsTo
    {
        return $this->belongsTo(EventPlan::class);
    }
}
