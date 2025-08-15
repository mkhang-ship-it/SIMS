<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ShortUrl extends Model
{
    use HasUuids;

    protected $fillable = [
        'club_id',
        'type',
        'original_url',
        'short_code',
        'title',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'type' => 'string'
    ];

    /**
     * Get the club that owns the short URL
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get all visits for this short URL
     */
    public function visits(): HasMany
    {
        return $this->hasMany(ShortUrlVisit::class);
    }

    /**
     * Check if the short URL is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Get the full short URL
     */
    public function getShortUrlAttribute(): string
    {
        return url($this->short_code);
    }

    /**
     * Get total visits count
     */
    public function getTotalVisitsAttribute(): int
    {
        return $this->visits()->count();
    }

    /**
     * Get unique visits count (by IP)
     */
    public function getUniqueVisitsAttribute(): int
    {
        return $this->visits()->distinct('ip')->count();
    }

    /**
     * Scope to get only active (non-expired) URLs
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Scope to get expired URLs
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by club
     */
    public function scopeForClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }
}
