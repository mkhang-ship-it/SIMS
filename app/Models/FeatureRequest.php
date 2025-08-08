<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeatureRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'club_id',
        'created_by',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'rejection_reason',
        'acknowledged_at',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'started_at'      => 'datetime',
        'completed_at'    => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
