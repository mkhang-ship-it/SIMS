<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Province extends Model
{
    use HasUuids;

    protected $fillable = ['code', 'name'];

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }
}
