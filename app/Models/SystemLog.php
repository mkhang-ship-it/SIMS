<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'method',
        'url',
        'ip',
        'user_agent',
        'request_data',
    ];

    protected $casts = [
        'request_data' => 'array',
    ];

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the method badge class.
     */
    public function getMethodBadgeClassAttribute(): string
    {
        return match ($this->method) {
            'GET' => 'bg-success',
            'POST' => 'bg-primary',
            'PUT' => 'bg-warning',
            'DELETE' => 'bg-danger',
            'PATCH' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the truncated URL for display.
     */
    public function getTruncatedUrlAttribute(): string
    {
        $url = (string) ($this->url ?? '');
        return strlen($url) > 80 ? substr($url, 0, 80) . '...' : $url;
    }

    /**
     * Get the truncated user agent for display.
     */
    public function getTruncatedUserAgentAttribute(): string
    {
        $userAgent = (string) ($this->user_agent ?? '');
        return strlen($userAgent) > 100 ? substr($userAgent, 0, 100) . '...' : $userAgent;
    }
}
