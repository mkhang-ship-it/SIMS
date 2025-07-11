<?php
// Role Model - Updated to include relationship with Club
// Added club() relationship to manage role-club associations

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'type', 'club_id', 'status', 'is_default', 'is_leader', 'is_admin'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Get the club that owns this role
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
