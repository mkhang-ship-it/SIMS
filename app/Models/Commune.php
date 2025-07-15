<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Commune extends Model
{
    use HasUuids;

    protected $fillable = ['code', 'name', 'province_id'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
