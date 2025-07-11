<?php
// Award Model - Club award and certificate management system
// Handles awards with multiple recipients, club relationships, and auto-generated award codes

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Award extends Model
{
    use HasUuids;

    protected $fillable = [
        'award_code',
        'title',
        'club_id',
        'semester_id',
        'award_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'award_date' => 'date',
    ];

    /**
     * Boot method to auto-generate award code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($award) {
            if (empty($award->award_code)) {
                $award->award_code = self::generateAwardCode();
            }
        });
    }

    /**
     * Generate unique award code in format BK-{year}-{6 random numbers}
     */
    public static function generateAwardCode()
    {
        do {
            $year = date('Y');
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $awardCode = "BK-{$year}-{$randomNumber}";
        } while (self::where('award_code', $awardCode)->exists());

        return $awardCode;
    }

    /**
     * Get the club that owns this award
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the semester this award belongs to
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get all recipients of this award (many-to-many relationship with pivot data)
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'award_recipients', 'award_id', 'user_id')
            ->withPivot('attachment')
            ->withTimestamps()
            ->using(AwardRecipient::class);
    }

    /**
     * Get all award recipients (direct relationship to pivot table)
     */
    public function awardRecipients()
    {
        return $this->hasMany(AwardRecipient::class);
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Đang xét duyệt',
            'awarded' => 'Đã trao',
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
            'pending' => 'warning',
            'awarded' => 'success',
            'revoked' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
