<?php
// Fixed: Updated table name from 'document_user_access' to 'document_user_accesses' to match migration

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentUserAccess extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'document_user_accesses';

    protected $fillable = [
        'document_id',
        'user_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
