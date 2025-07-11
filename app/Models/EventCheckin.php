<?php
// EventCheckin Model - QR Code Check-in system for events
// Handles member check-ins with geolocation validation and time constraints
// Features: unique check-in per user/event, distance calculation, and user agent tracking

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EventCheckin extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_id',
        'user_id',
        'checkin_time',
        'user_agent',
        'latitude',
        'longitude',
        'distance'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the event that this check-in belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who performed this check-in
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate distance between two geographical points using Haversine formula
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in kilometers
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if user can check-in to event based on various constraints
     * @param Event $event
     * @param User $user
     * @param float $userLat User's current latitude
     * @param float $userLng User's current longitude
     * @return array ['success' => bool, 'message' => string, 'distance' => float|null]
     */
    public static function canCheckIn($event, $user, $userLat, $userLng)
    {
        // Check if event is active
        if ($event->status !== 'active') {
            return ['success' => false, 'message' => 'Sự kiện chưa được duyệt hoặc đã bị hủy.', 'distance' => null];
        }

        // Check if current time is within event time
        if (!$event->shouldShowQRCode()) {
            return ['success' => false, 'message' => 'Không trong thời gian diễn ra sự kiện.', 'distance' => null];
        }

        // Check if user already checked in
        $existingCheckin = self::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingCheckin) {
            return ['success' => false, 'message' => 'Bạn đã check-in cho sự kiện này rồi.', 'distance' => null];
        }

        // Check distance if classroom has location data
        if ($event->classRoom && $event->classRoom->latitude && $event->classRoom->longitude) {
            $distance = self::calculateDistance(
                $userLat,
                $userLng,
                $event->classRoom->latitude,
                $event->classRoom->longitude
            );

            if ($distance > 5) { // 5km radius
                return ['success' => false, 'message' => "Bạn đang ở quá xa địa điểm sự kiện ({$distance} km). Vui lòng di chuyển gần hơn (trong bán kính 5km).", 'distance' => $distance];
            }

            return ['success' => true, 'message' => 'Có thể check-in.', 'distance' => $distance];
        }

        // If no location data, allow check-in but warn
        return ['success' => true, 'message' => 'Có thể check-in (không kiểm tra vị trí).', 'distance' => null];
    }

    /**
     * Get formatted checkin time
     */
    public function getFormattedCheckinTimeAttribute()
    {
        return $this->checkin_time->format('d/m/Y H:i:s');
    }

    /**
     * Get browser name from user agent
     */
    public function getBrowserNameAttribute()
    {
        if (!$this->user_agent) {
            return 'Không xác định';
        }

        $userAgent = $this->user_agent;

        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }

        return 'Khác';
    }

    /**
     * Scope to get check-ins for a specific event
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope to get check-ins for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get check-ins within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('checkin_time', [$startDate, $endDate]);
    }
}
