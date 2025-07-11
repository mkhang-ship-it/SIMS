<?php
// EventActionPlan Model - Action Plans Management for Event Plans
// Manages detailed action plans for events with activities, responsibilities, timelines, and risk management
// Created: Complete model with relationships to EventPlan, User for primary responsible and support members
// Includes JSON support for support_members array and proper datetime handling

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventActionPlan extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_plan_id',
        'activity_item',
        'expected_result',
        'start_time',
        'end_time',
        'primary_responsible_id',
        'support_members',
        'potential_risks',
        'risk_prevention_actions'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'support_members' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event plan that owns this action plan
     */
    public function eventPlan(): BelongsTo
    {
        return $this->belongsTo(EventPlan::class);
    }

    /**
     * Get the primary responsible user for this action plan
     */
    public function primaryResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_responsible_id');
    }

    /**
     * Get support members for this action plan
     * Returns collection of User models based on support_members array
     */
    public function supportMembersUsers()
    {
        if (empty($this->support_members)) {
            return collect();
        }

        return User::whereIn('id', $this->support_members)->get();
    }

    /**
     * Get formatted support members names as string
     */
    public function getSupportMembersNamesAttribute(): string
    {
        return $this->supportMembersUsers()->pluck('name')->join(', ');
    }

    /**
     * Get formatted duration as string
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return 'Chưa xác định';
        }

        $start = $this->start_time;
        $end = $this->end_time;
        $diff = $start->diff($end);

        if ($diff->days > 0) {
            return $diff->days . ' ngày ' . $diff->h . ' giờ';
        } elseif ($diff->h > 0) {
            return $diff->h . ' giờ ' . $diff->i . ' phút';
        } else {
            return $diff->i . ' phút';
        }
    }

    /**
     * Check if action plan is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_time && $this->end_time->isPast();
    }

    /**
     * Check if action plan is in progress
     */
    public function getIsInProgressAttribute(): bool
    {
        if (!$this->start_time || !$this->end_time) {
            return false;
        }

        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->is_overdue) {
            return 'danger';
        } elseif ($this->is_in_progress) {
            return 'success';
        } else {
            return 'warning';
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_overdue) {
            return 'Quá hạn';
        } elseif ($this->is_in_progress) {
            return 'Đang thực hiện';
        } else {
            return 'Chưa bắt đầu';
        }
    }
}
