<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['module_id', 'name', 'display_name'];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
