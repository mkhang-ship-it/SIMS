<?php

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
        'student_code',
        'major_id',
        'course_id',
        'role_id',
        'type',
        'status',
        'default_club_id', // <--- thêm dòng này
    ];

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user', 'user_id', 'chat_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function groupChats()
    {
        return $this->chats()->where('is_group', true);
    }

    public function privateChats()
    {
        return $this->chats()->where('is_group', false);
    }

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

    /**
     * Get the bank account for this user
     */
    public function bankAccount()
    {
        return $this->hasOne(UserBankAccount::class);
    }

    // Updated: hasPermissionForModuleCode now checks admin via user role_id, club user via UserClubRole (by session club_id)
    public function hasPermissionForModuleCode($moduleCode, $action, $roleType = null)
    {
        // Lấy club_id từ session nếu có (dùng cho user club)
        $clubId = session('club_id');

        // Nếu là admin (role type central hoặc không có club_id), check theo role_id của User
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

        // Nếu là user club (role type branch), check theo UserClubRole của club hiện tại
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

        // Mặc định không có quyền
        return false;
    }
}
