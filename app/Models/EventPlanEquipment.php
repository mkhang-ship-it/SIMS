<?php
// EventPlanEquipment Model - Equipment Management for Event Plans
// Created: Equipment management system linked to event_plans table
// Includes: name, quantity (integer), type (club/school), notes
// Relationships: belongs to EventPlan

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlanEquipment extends Model
{
    use HasUuids;

    protected $table = 'event_plan_equipment';

    protected $fillable = [
        'event_plan_id',
        'name',
        'quantity',
        'type',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event plan that owns this equipment
     */
    public function eventPlan(): BelongsTo
    {
        return $this->belongsTo(EventPlan::class);
    }

    /**
     * Get type label for display
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'club' => 'Câu lạc bộ',
            'school' => 'Trường học',
            default => $this->type
        };
    }

    /**
     * Get type color for badge display
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'club' => 'primary',
            'school' => 'success',
            default => 'secondary'
        };
    }
}
