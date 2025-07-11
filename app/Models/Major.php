<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Major extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'parent_id',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Major::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Major::class, 'parent_id');
    }
}
