<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Module extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['id', 'module_code', 'name', 'type'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
