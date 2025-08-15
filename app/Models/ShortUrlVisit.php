<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ShortUrlVisit extends Model
{
    protected $fillable = [
        'short_url_id',
        'ip',
        'user_agent',
        'referrer',
        'visited_at'
    ];

    protected $casts = [
        'visited_at' => 'datetime'
    ];

    /**
     * Disable timestamps since we have visited_at field
     */
    public $timestamps = false;

    /**
     * Get the short URL that was visited
     */
    public function shortUrl(): BelongsTo
    {
        return $this->belongsTo(ShortUrl::class);
    }

    /**
     * Scope to get visits from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', Carbon::today());
    }

    /**
     * Scope to get visits from yesterday
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('visited_at', Carbon::yesterday());
    }

    /**
     * Scope to get visits from last 7 days
     */
    public function scopeLastWeek($query)
    {
        return $query->where('visited_at', '>=', Carbon::now()->subWeek());
    }

    /**
     * Scope to get visits from last 30 days
     */
    public function scopeLastMonth($query)
    {
        return $query->where('visited_at', '>=', Carbon::now()->subMonth());
    }

    /**
     * Scope to get visits by IP address
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }

    /**
     * Scope to get visits with referrer
     */
    public function scopeWithReferrer($query)
    {
        return $query->whereNotNull('referrer');
    }

    /**
     * Get browser name from user agent
     */
    public function getBrowserAttribute(): string
    {
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
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceTypeAttribute(): string
    {
        $userAgent = $this->user_agent;

        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }
}
