<?php
// Created: UserClubRole model for user-club-role pivot table (multi-club membership)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserClubRole extends Model
{
    use HasUuids;

    protected $table = 'user_club_roles';

    protected $fillable = [
        'user_id',
        'club_id',
        'role_id',
        'joined_at',
        'status',
    ];

    /**
     * Get the user for this membership
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the club for this membership
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the role for this membership
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope to get user club roles by club
     */
    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }
}
