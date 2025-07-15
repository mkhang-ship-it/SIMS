<?php
// Updated: AwardRecipient Model - Explicitly set table name to 'award_recipients' to avoid singular/plural issues in queries
// Handles many-to-many relationship between awards and user_club_roles with individual attachment field

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwardRecipient extends Pivot
{
    use HasUuids;

    // Fix: Explicitly set table name to avoid Eloquent using 'award_recipient' (singular)
    protected $table = 'award_recipients';

    protected $fillable = [
        'award_id',
        'user_club_role_id',
        'attachment'
    ];

    /**
     * Get the award this recipient belongs to
     */
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    /**
     * Get the user_club_role (membership) this record belongs to
     */
    public function userClubRole(): BelongsTo
    {
        return $this->belongsTo(UserClubRole::class);
    }

    /**
     * Get the user for this recipient (shortcut)
     */
    public function user()
    {
        return $this->userClubRole ? $this->userClubRole->user() : null;
    }
}
