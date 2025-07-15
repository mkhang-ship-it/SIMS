<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Chat extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['is_group', 'name', 'created_by'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
