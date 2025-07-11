<?php
// EventImage Model - Manages multiple images for each event
// Supports image upload, storage, and display with proper relationships
// Added: UUID primary key, Event relationship, and file management capabilities

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EventImage extends Model
{
    use HasUuids;

    protected $fillable = [
        'event_id',
        'image_path',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
