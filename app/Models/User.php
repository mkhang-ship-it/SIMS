<?php
// Updated: Added complete user profile fields to support full member management
// Fillable fields: personal info, contact details, academic info, system links
// Added relationships: address, major, course, invoices for proper data associations
// Added checkins relationship for event checkin management

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\EventCheckin;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'gender',
        'birthday',
        'identity_card_code',
        'phone',
        'address_id',
        'student_code',
        'major_id',
        'course_id',
        'role_id',
        'club_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get all awards received by this user
     */
    public function awards()
    {
        return $this->belongsToMany(Award::class, 'award_recipients', 'user_id', 'award_id')
            ->withPivot('attachment')
            ->withTimestamps()
            ->using(AwardRecipient::class);
    }

    /**
     * Get all award recipients records for this user
     */
    public function awardRecipients()
    {
        return $this->hasMany(AwardRecipient::class);
    }

    /**
     * Get all invoices for this user
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all event checkins for this user
     */
    public function checkins()
    {
        return $this->hasMany(EventCheckin::class);
    }

    public function hasPermissionForModuleCode($moduleCode, $action, $roleType = null)
    {
        $query = $this->role()
            ->with('permissions.module');

        // If role type is specified, filter by role type
        if ($roleType !== null) {
            $query->where('type', $roleType);
        }

        $role = $query->first();

        // If no role found (when filtering by type), return false
        if (!$role) {
            return false;
        }

        return $role->permissions()
            ->whereHas('module', function ($q) use ($moduleCode) {
                $q->where('module_code', $moduleCode);
            })
            ->where('name', $action)
            ->exists();
    }
}
