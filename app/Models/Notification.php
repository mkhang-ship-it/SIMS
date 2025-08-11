<?php
// Created: Notification model for admin notification system
// Added relationships with User (sender) and Club (targets)
// Added methods for managing notification reads and target clubs
// Updated: Added image_path and files_path fields for image and file attachments
// Updated: Fixed getUnreadForClub method to properly handle 'all_clubs' target type when target_club_ids is null

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'files_path',
        'target_type',
        'target_club_ids',
        'sender_id',
        'status'
    ];

    protected $casts = [
        'target_club_ids' => 'array',
        'files_path' => 'array',
    ];

    /**
     * Get the admin who sent this notification
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the clubs that have read this notification
     */
    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    /**
     * Get the target clubs for this notification
     */
    public function targetClubs()
    {
        if ($this->target_type === 'all_clubs') {
            return Club::all();
        }

        if ($this->target_club_ids) {
            return Club::whereIn('id', $this->target_club_ids)->get();
        }

        return collect();
    }

    /**
     * Check if a specific club has read this notification
     */
    public function isReadByClub($clubId)
    {
        return $this->reads()->where('club_id', $clubId)->whereNotNull('read_at')->exists();
    }

    /**
     * Mark notification as read by a club
     */
    public function markAsReadByClub($clubId)
    {
        $read = $this->reads()->firstOrCreate(['club_id' => $clubId]);
        if (!$read->read_at) {
            $read->update(['read_at' => now()]);
        }
    }

    /**
     * Get unread notifications for a club
     */
    public static function getUnreadForClub($clubId)
    {
        return static::where(function ($query) use ($clubId) {
            $query->where('target_type', 'all_clubs')
                ->orWhere(function ($subQuery) use ($clubId) {
                    $subQuery->where('target_type', 'specific_clubs')
                        ->whereJsonContains('target_club_ids', $clubId);
                });
        })
            ->where('status', 'active')
            ->whereDoesntHave('reads', function ($query) use ($clubId) {
                $query->where('club_id', $clubId)->whereNotNull('read_at');
            })
            ->orderBy('created_at', 'desc');
    }
}
