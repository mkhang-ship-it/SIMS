<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'messages';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['sender_id', 'chat_id', 'content', 'type', 'file_path'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
