<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolPoint extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'club_id',
        'semester_id',
        'club_points',
        'school_points',
        'is_leader'
    ];

    protected $casts = [
        'club_points' => 'decimal:2',
        'school_points' => 'decimal:2',
        'is_leader' => 'boolean'
    ];

    /**
     * Get the user this school point belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the club this school point belongs to
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the semester this school point belongs to
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope to get school points by club
     */
    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    /**
     * Scope to get school points by semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Scope to get school points by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted club points with sign
     */
    public function getFormattedClubPointsAttribute()
    {
        if ($this->club_points >= 0) {
            return '+' . number_format($this->club_points, 1);
        }
        return number_format($this->club_points, 1);
    }

    /**
     * Get formatted school points
     */
    public function getFormattedSchoolPointsAttribute()
    {
        return number_format($this->school_points, 1);
    }

    /**
     * Calculate and update school points for all members in a club and semester
     */
    public static function calculateAndUpdateSchoolPoints($clubId, $semesterId)
    {
        // Get all club members with their roles
        $clubMembers = UserClubRole::with(['user', 'role'])
            ->byClub($clubId)
            ->where('status', 'active')
            ->get();

        if ($clubMembers->isEmpty()) {
            return;
        }

        // Calculate club points for each member
        $memberPoints = [];
        $maxClubPoints = 0;

        foreach ($clubMembers as $member) {
            $clubPoints = DisciplineRecord::calculateTotalPoints(
                $member->user_id,
                $semesterId,
                $clubId
            );

            $memberPoints[$member->user_id] = [
                'club_points' => $clubPoints,
                'is_leader' => $member->role && $member->role->is_leader === 'leader'
            ];

            // Track maximum club points (for negative points, we want the least negative/highest value)
            if ($clubPoints > $maxClubPoints) {
                $maxClubPoints = $clubPoints;
            }
        }

        // If all members have 0 or negative points, set max to 1 to avoid division by zero
        if ($maxClubPoints <= 0) {
            $maxClubPoints = 1;
        }

        // Calculate and save school points for each member
        foreach ($memberPoints as $userId => $data) {
            $clubPoints = $data['club_points'];
            $isLeader = $data['is_leader'];

            // Calculate base school points (scaled to max 5 points)
            $baseSchoolPoints = ($clubPoints / $maxClubPoints) * 5;

            // Ensure minimum 0 points
            if ($baseSchoolPoints < 0) {
                $baseSchoolPoints = 0;
            }

            // Add leader bonus (+5 points)
            $finalSchoolPoints = $baseSchoolPoints;
            if ($isLeader) {
                $finalSchoolPoints += 5;
            }

            // Update or create school point record
            static::updateOrCreate(
                [
                    'user_id' => $userId,
                    'club_id' => $clubId,
                    'semester_id' => $semesterId
                ],
                [
                    'club_points' => $clubPoints,
                    'school_points' => round($finalSchoolPoints, 2),
                    'is_leader' => $isLeader
                ]
            );
        }
    }

    /**
     * Get school point for a specific user, club, and semester
     */
    public static function getSchoolPoint($userId, $clubId, $semesterId)
    {
        return static::byUser($userId)
            ->byClub($clubId)
            ->bySemester($semesterId)
            ->first();
    }

    /**
     * Recalculate school points for a user when their club points change
     */
    public static function recalculateForUser($userId, $clubId, $semesterId)
    {
        // Recalculate for the entire club to maintain proper ratios
        static::calculateAndUpdateSchoolPoints($clubId, $semesterId);
    }
}
