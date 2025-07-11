<?php
// EventPlan Model - Event Plan Management System
// Auto-generated when creating events - manages comprehensive event planning information
// Updated: Added complete relationships with Event, Club, User and fillable fields for all plan components
// Updated: Changed implementation_time field from text to date for proper date handling
// Updated: Changed participants, responsible_organizer, advisor_coordinator from text to string for better input handling
// Updated: Added relationship with EventPlanObjective for objectives and measurement indicators management
// Updated: Added relationship with EventBudgetItem for budget cost management
// Updated: Added relationship with EventPlanEquipment for equipment management
// Updated: Added relationship with EventPlanTimeline for timeline management with drag/drop ordering

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPlan extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_id',
        'club_id',
        'prepared_by',
        'implementation_time',
        'approved_by',
        'participants',
        'description',
        'responsible_organizer',
        'advisor_coordinator'
    ];

    protected $casts = [
        'implementation_time' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event that owns this event plan
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the club that owns this event plan
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the user who prepared this event plan
     */
    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * Get all objectives for this event plan
     */
    public function objectives()
    {
        return $this->hasMany(EventPlanObjective::class);
    }

    /**
     * Get all action plans for this event plan
     */
    public function actionPlans()
    {
        return $this->hasMany(EventActionPlan::class);
    }

    /**
     * Get all budget items for this event plan
     */
    public function budgetItems()
    {
        return $this->hasMany(EventPlanBudgetItem::class);
    }

    /**
     * Get all equipment for this event plan
     */
    public function equipment()
    {
        return $this->hasMany(EventPlanEquipment::class);
    }

    /**
     * Get all timeline items for this event plan
     */
    public function timeline()
    {
        return $this->hasMany(EventPlanTimeline::class)->ordered();
    }
}
