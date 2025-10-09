<?php

/*
 * Updated: Fixed table name references from 'document_user_access' to 'document_user_accesses'
 * in the viewers() and accessUsers() methods to match the actual table created by migrations.
 * This fixes the SQLSTATE[42S02] error when querying documents with restricted visibility.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Document extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'documents';

    protected $fillable = [
        'club_id',
        'document_type_id',
        'uploaded_by',
        'title',
        'file_path',
        'visibility',
        'status',
        'reason_rejected',
    ];

    // Thuộc loại văn bản nào
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    // Người upload tài liệu
    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Người được phép xem tài liệu (restricted)
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'document_user_accesses', 'document_id', 'user_id');
    }

    // Nếu tài liệu này là kết quả của yêu cầu upload
    public function requestUser()
    {
        return $this->hasOne(DocumentRequestUser::class, 'document_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function accessUsers()
    {
        return $this->belongsToMany(User::class, 'document_user_accesses', 'document_id', 'user_id');
    }
}
