<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplineRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'club_id',
        'discipline_id',
        'semester_id',
        'applied_date',
        'status',
        'applied_by',
        'notes'
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    /**
     * Get the user who received the discipline (người được ghi nhận điểm)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who applied the discipline (người ghi nhận điểm)
     */
    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Get the club this discipline record belongs to
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the discipline type
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    /**
     * Get the semester this discipline record belongs to
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => 'Hoạt động',
            'revoked' => 'Đã thu hồi'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'danger',
            'revoked' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted points with sign from discipline
     */
    public function getFormattedPointsAttribute()
    {
        $points = $this->discipline->discipline_points;
        if ($points >= 0) {
            return '+' . $points;
        }
        return (string) $points;
    }

    /**
     * Scope to get active discipline records
     */
    public function scopeActive($query)
    {
        return $query->where('discipline_records.status', 'active');
    }

    /**
     * Scope to get records by semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('discipline_records.semester_id', $semesterId);
    }

    /**
     * Scope to get records by club
     */
    public function scopeByClub($query, $clubId)
    {
        return $query->where('discipline_records.club_id', $clubId);
    }

    /**
     * Scope to get records by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('discipline_records.user_id', $userId);
    }

    /**
     * Calculate total discipline points for a user in a specific semester and club
     */
    public static function calculateTotalPoints($userId, $semesterId, $clubId)
    {
        return static::active()
            ->byUser($userId)
            ->bySemester($semesterId)
            ->byClub($clubId)
            ->join('disciplines', 'discipline_records.discipline_id', '=', 'disciplines.id')
            ->sum('disciplines.discipline_points');
    }
}
