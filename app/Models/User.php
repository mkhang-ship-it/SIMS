<?php
// [2024-12-19] Fixed: Updated awards and awardRecipients relationships to use proper hasManyThrough with user_club_role_id

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\EventCheckin;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

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
        'student_code',
        'major_id',
        'course_id',
        'role_id',
        'type',
        'status',
        'default_club_id',
        'github_id',
        'microsoft_id',
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
            'birthday' => 'date',
        ];
    }

    /**
     * Check if user account is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => 'Hoạt động',
            'inactive' => 'Vô hiệu hóa'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Quan hệ: user thuộc nhiều club với nhiều role (multi-club)
     */
    public function userClubRoles()
    {
        return $this->hasMany(UserClubRole::class);
    }

    /**
     * Quan hệ: user thuộc nhiều club (dùng belongsToMany qua user_club_roles)
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'user_club_roles', 'user_id', 'club_id')
            ->withPivot(['role_id', 'joined_at', 'status', 'id'])
            ->withTimestamps();
    }

    /**
     * Quan hệ: club mặc định của user
     */
    public function defaultClub()
    {
        return $this->belongsTo(Club::class, 'default_club_id');
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
     * Get the address for this user
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Get all awards received by this user through user_club_roles
     */
    public function awards()
    {
        return $this->hasManyThrough(
            Award::class,
            UserClubRole::class,
            'user_id', // Foreign key on user_club_roles table
            'id', // Foreign key on awards table
            'id', // Local key on users table
            'id' // Local key on user_club_roles table
        )->join('award_recipients', function ($join) {
            $join->on('awards.id', '=', 'award_recipients.award_id')
                ->on('user_club_roles.id', '=', 'award_recipients.user_club_role_id');
        });
    }

    /**
     * Get all award recipients records for this user
     */
    public function awardRecipients()
    {
        return $this->hasManyThrough(
            AwardRecipient::class,
            UserClubRole::class,
            'user_id', // Foreign key on user_club_roles table
            'user_club_role_id', // Foreign key on award_recipients table
            'id', // Local key on users table
            'id' // Local key on user_club_roles table
        );
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

    /**
     * Get the bank account for this user
     */
    public function bankAccount()
    {
        return $this->hasOne(UserBankAccount::class);
    }

    public function hasPermissionForModuleCode($moduleCode, $action, $roleType = null)
    {
        $clubId = session('club_id');

        if ($this->role && ($this->role->type === 'central' || !$clubId)) {
            $role = $this->role()->with('permissions.module');
            if ($roleType !== null) {
                $role->where('type', $roleType);
            }
            $role = $role->first();
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

        if ($clubId) {
            $userClubRole = $this->userClubRoles()
                ->where('club_id', $clubId)
                ->with('role.permissions.module')
                ->first();
            if (!$userClubRole || !$userClubRole->role) {
                return false;
            }
            $role = $userClubRole->role;
            if ($roleType !== null && $role->type !== $roleType) {
                return false;
            }
            return $role->permissions()
                ->whereHas('module', function ($q) use ($moduleCode) {
                    $q->where('module_code', $moduleCode);
                })
                ->where('name', $action)
                ->exists();
        }

        return false;
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestUser::class, 'user_id');
    }

    public function accessibleDocuments()
    {
        return $this->belongsToMany(Document::class, 'document_user_access', 'user_id', 'document_id');
    }

    public function getFullAddressAttribute()
    {
        if (!$this->address) {
            return '';
        }
        return $this->address->full_address;
    }
}
