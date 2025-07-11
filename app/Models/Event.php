<?php
// Event Model - Club event management system with updated status workflow
// Handles events with auto-generated event codes, QR hash for checkin, single slot timing, and specific event dates
// Updated to include event_date field for precise scheduling and QR code time validation
// Updated: Changed status from 2 states to 4 states workflow (pending, active, cancelled, completed)
// Updated: Added relationship with EventImage model for multiple image management

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_code',
        'name',
        'description',
        'image',
        'qr_hash',
        'club_id',
        'semester_id',
        'slot_id',
        'event_date',
        'class_room_id',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate event code and QR hash
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_code)) {
                $event->event_code = self::generateEventCode();
            }
            if (empty($event->qr_hash)) {
                $event->qr_hash = self::generateQrHash();
            }
        });
    }

    /**
     * Generate unique event code in format EVT-{year}-{6 random numbers}
     */
    public static function generateEventCode()
    {
        do {
            $year = date('Y');
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $eventCode = "EVT-{$year}-{$randomNumber}";
        } while (self::where('event_code', $eventCode)->exists());

        return $eventCode;
    }

    /**
     * Generate unique QR hash for checkin
     */
    public static function generateQrHash()
    {
        do {
            $qrHash = Str::random(32);
        } while (self::where('qr_hash', $qrHash)->exists());

        return $qrHash;
    }

    /**
     * Get the club that owns this event
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the semester this event belongs to
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the slot for this event
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the classroom for this event
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * Get all check-ins for this event
     */
    public function checkins()
    {
        return $this->hasMany(EventCheckin::class);
    }

    /**
     * Get all images for this event
     */
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    /**
     * Get the event plan for this event
     */
    public function eventPlan()
    {
        return $this->hasOne(EventPlan::class);
    }
    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ Duyệt',
            'active' => 'Đã Duyệt',
            'cancelled' => 'Đã Hủy',
            'completed' => 'Hoàn Thành'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'active' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get available status options for user based on current status and user role
     */
    public function getAvailableStatusOptions($userRole = 'user')
    {
        // If completed, no status changes allowed
        if ($this->status === 'completed') {
            return [];
        }

        // For admin/super admin, they can approve pending events to active
        if (in_array($userRole, ['admin', 'super_admin'])) {
            $options = [];

            if ($this->status === 'pending') {
                $options['active'] = 'Duyệt Sự Kiện';
                $options['cancelled'] = 'Hủy Sự Kiện';
            } elseif ($this->status === 'active') {
                $options['active'] = 'Đã Duyệt';
                $options['cancelled'] = 'Hủy Sự Kiện';
            } elseif ($this->status === 'cancelled') {
                $options['pending'] = 'Chờ Duyệt';
                $options['cancelled'] = 'Đã Hủy';
            }

            return $options;
        }

        // For regular users
        switch ($this->status) {
            case 'pending':
                return [
                    'pending' => 'Chờ Duyệt',
                    'cancelled' => 'Hủy Sự Kiện'
                ];
            case 'active':
                return [
                    'active' => 'Đã Duyệt',
                    'cancelled' => 'Hủy Sự Kiện'
                ];
            case 'cancelled':
                return [
                    'pending' => 'Chờ Duyệt',
                    'cancelled' => 'Đã Hủy'
                ];
            default:
                return [];
        }
    }

    /**
     * Check if status can be changed
     */
    public function canChangeStatus()
    {
        return $this->status !== 'completed';
    }

    /**
     * Check if event can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'active']);
    }

    /**
     * Check if event can be deleted
     */
    public function canBeDeleted()
    {
        return in_array($this->status, ['pending', 'cancelled']);
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        if ($this->slot) {
            return $this->slot->start_time . ' - ' . $this->slot->end_time;
        }
        return '-';
    }

    /**
     * Check if QR code should be displayed (only during event time and active status)
     */
    public function shouldShowQRCode()
    {
        // Only show QR code for active events
        if ($this->status !== 'active') {
            return false;
        }

        // If no event_date or slot, don't show QR code
        if (!$this->event_date || !$this->slot) {
            return false;
        }

        $now = Carbon::now();
        $eventDate = Carbon::parse($this->event_date);

        // Check if today is the event date
        if (!$now->isSameDay($eventDate)) {
            return false;
        }

        // Parse slot times for today
        $startTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->slot->start_time);
        $endTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->slot->end_time);

        // Check if current time is within the event slot time
        return $now->between($startTime, $endTime);
    }

    /**
     * Check if event has ended (for auto completion by cronjob)
     */
    public function hasEventEnded()
    {
        // If not active, don't consider for completion
        if ($this->status !== 'active') {
            return false;
        }

        // If no event_date or slot, consider as ended
        if (!$this->event_date || !$this->slot) {
            return true;
        }

        $now = Carbon::now();
        $eventDate = Carbon::parse($this->event_date);

        // If event date is in the past
        if ($eventDate->isPast() && !$now->isSameDay($eventDate)) {
            return true;
        }

        // If today is event date, check if end time has passed
        if ($now->isSameDay($eventDate)) {
            $endTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->slot->end_time);
            return $now->isAfter($endTime);
        }

        return false;
    }

    /**
     * Get formatted event date in Vietnamese
     */
    public function getFormattedEventDateAttribute()
    {
        if (!$this->event_date) {
            return '-';
        }

        return Carbon::parse($this->event_date)->format('d/m/Y');
    }
}
