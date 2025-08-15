<?php
// Updated: Đổi tên các trường image_vertical_light và image_vertical_dark thành image_square_light và image_square_dark trong $fillable
// Added roles(), awards(), invoices() and events() relationships to manage club-specific data
// Updated: Thêm quan hệ userClubRoles (hasMany) và users (belongsToMany qua user_club_roles) cho multi-club
// Added: address() relationship to support club addresses

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Club extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'image_horizontal_light',
        'image_horizontal_dark',
        'image_square_light',
        'image_square_dark'
    ];

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

    /**
     * Get the club's address
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Quan hệ: club có nhiều userClubRoles (pivot)
     */
    public function userClubRoles()
    {
        return $this->hasMany(UserClubRole::class);
    }

    /**
     * Quan hệ: club có nhiều user (dùng belongsToMany qua user_club_roles)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_club_roles', 'club_id', 'user_id')
            ->withPivot(['role_id', 'joined_at', 'status', 'id'])
            ->withTimestamps();
    }

    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }
}
