<?php
// EventPlanObjective Model - Event Plan Objectives Management
// Manages objectives and measurement indicators for event plans
// Relationships: belongs to EventPlan

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlanObjective extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_plan_id',
        'objective',
        'expected_result',
        'responsible_party'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event plan that owns this objective
     */
    public function eventPlan(): BelongsTo
    {
        return $this->belongsTo(EventPlan::class);
    }
}
