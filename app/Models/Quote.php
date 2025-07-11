<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Quote extends Model
{
    use HasUuids;

    protected $fillable = ['quote', 'author', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
