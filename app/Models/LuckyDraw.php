<?php
// Updated: Added modal_background_image field to LuckyDraw model for customizing the spinning wheel modal background
// - Added security_code field to fillable array and updated participant winner checking methods
// - Added hasParticipantWon() method to check if a participant has already won
// - Updated getAvailableParticipants() to be more efficient
// - Added getAvailableParticipantsForPrize() method to get participants available for a specific prize

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class LuckyDraw extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'club_id',
        'type',
        'title',
        'slug',
        'security_code',
        'participants',
        'winners',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'background_image',
        'modal_background_image'
    ];

    protected $casts = [
        'participants' => 'array',
        'winners' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($luckyDraw) {
            if (empty($luckyDraw->slug)) {
                $luckyDraw->slug = Str::slug($luckyDraw->title) . '-' . Str::random(8);
            }
        });
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class)->orderBy('order');
    }

    public function isActive()
    {
        return $this->is_active &&
            $this->start_date <= now() &&
            $this->end_date >= now();
    }

    public function isExpired()
    {
        return $this->end_date < now();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function addWinner($participantName, $prizeName = '')
    {
        $winners = $this->winners ?? [];
        $winners[] = [
            'name' => $participantName,
            'prize' => $prizeName,
            'won_at' => now()->toDateTimeString()
        ];
        $this->update(['winners' => $winners]);
    }

    public function clearWinners()
    {
        $this->update(['winners' => []]);
    }

    public function hasParticipantWon($participantName)
    {
        $winners = $this->winners ?? [];
        return collect($winners)->contains('name', $participantName);
    }

    public function getAvailableParticipants()
    {
        $participants = $this->participants ?? [];
        $winners = collect($this->winners ?? [])->pluck('name')->toArray();

        return array_filter($participants, function ($participant) use ($winners) {
            return !in_array($participant, $winners);
        });
    }

    public function getAvailableParticipantsForPrize($prizeName)
    {
        $participants = $this->participants ?? [];
        $winners = $this->winners ?? [];

        // Get participants who haven't won yet
        $availableParticipants = array_filter($participants, function ($participant) use ($winners) {
            return !collect($winners)->contains('name', $participant);
        });

        // If prize is specified, check if it's still available
        if ($prizeName) {
            $prize = $this->prizes()->where('name', $prizeName)->first();
            if (!$prize || !$prize->isAvailable()) {
                return []; // Prize is not available
            }
        }

        return $availableParticipants;
    }
}
