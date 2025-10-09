<?php

/**
 * Fixed: Added $casts property to cast 'deadline' field as datetime to enable Carbon date methods like format()
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentRequest extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'document_requests';

    protected $fillable = [
        'club_id',
        'document_type_id',
        'requested_by',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function documentRequestUsers()
    {
        return $this->hasMany(DocumentRequestUser::class, 'document_request_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
