<?php
// Created: NotificationRead model to track which clubs have read notifications
// Added relationships with Notification and Club models

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NotificationRead extends Model
{
    use HasUuids;

    protected $fillable = [
        'notification_id',
        'club_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the notification that was read
     */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the club that read the notification
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
