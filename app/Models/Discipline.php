<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discipline extends Model
{
    use HasUuids;

    protected $fillable = [
        'club_id',
        'name',
        'code',
        'discipline_points',
        'description',
        'status'
    ];

    /**
     * Boot method to auto-generate discipline code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($discipline) {
            if (empty($discipline->code)) {
                $discipline->code = self::generateDisciplineCode($discipline->club_id);
            }
        });
    }

    /**
     * Generate unique discipline code in format KL-{6 random numbers}
     */
    public static function generateDisciplineCode($clubId)
    {
        do {
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $disciplineCode = "KL-{$randomNumber}";
        } while (self::where('club_id', $clubId)->where('code', $disciplineCode)->exists());

        return $disciplineCode;
    }

    /**
     * Get the club that owns this discipline
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => 'Hoạt động',
            'inactive' => 'Tạm ngưng'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get discipline points with sign
     */
    public function getFormattedPointsAttribute()
    {
        if ($this->discipline_points >= 0) {
            return '+' . $this->discipline_points;
        }
        return (string) $this->discipline_points;
    }

    /**
     * Scope to get disciplines by club
     */
    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }
}
