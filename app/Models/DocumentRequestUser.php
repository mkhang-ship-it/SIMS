<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentRequestUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'document_request_users';

    protected $fillable = [
        'document_request_id',
        'user_id',
        'document_id',
    ];

    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class, 'document_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}