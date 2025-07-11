<?php
// Updated: Added events() relationship to Club model to support 3-step filter flow for event images
// Added roles(), awards(), invoices() and events() relationships to manage club-specific data

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Club extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'email', 'phone', 'address', 'status', 'image'];

    /**
     * Get all roles for this club
     */
    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get all awards for this club
     */
    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    /**
     * Get all invoices for this club
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all events for this club
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
