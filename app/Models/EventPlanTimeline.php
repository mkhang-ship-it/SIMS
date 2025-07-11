<?php
// Created: 2025-01-23
// Changes: Created EventPlanTimeline model for timeline management with UUID support and relationships
// Updated: 2025-01-23 - Fixed getDurationInMinutesAttribute method: changed from $end->diffInMinutes($start) to $start->diffInMinutes($end) to calculate correct positive duration

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlanTimeline extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_plan_timeline';

    protected $fillable = [
        'event_plan_id',
        'date',
        'start_time',
        'end_time',
        'content',
        'order_index',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'order_index' => 'integer',
    ];

    /**
     * Get the event plan that owns the timeline item.
     */
    public function eventPlan(): BelongsTo
    {
        return $this->belongsTo(EventPlan::class, 'event_plan_id');
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get time duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time->format('H:i:s'));
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time->format('H:i:s'));

        return $start->diffInMinutes($end);
    }

    /**
     * Scope to order by order_index.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index')->orderBy('date')->orderBy('start_time');
    }
}
