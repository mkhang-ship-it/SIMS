<?php
// AwardRecipient Model - Pivot model for award recipients with individual attachments
// Handles many-to-many relationship between awards and users with additional attachment field

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwardRecipient extends Model
{
    use HasUuids;

    protected $fillable = [
        'award_id',
        'user_id',
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
     * Get the user (recipient) this record belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
