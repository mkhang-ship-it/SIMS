<?php
// Expense Model - Expense management system for clubs
// Features: UUID primary key, club/semester relationships, approval workflow
// Tracks: expense requests, approvals, payments, status management

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'amount',
        'requested_by',
        'approved_by',
        'approved_at',
        'requested_at',
        'status',
        'club_id',
        'semester_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'requested_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Chờ phê duyệt',
            'approved' => 'Đã phê duyệt',
            'rejected' => 'Từ chối',
            'paid' => 'Đã thanh toán',
            default => 'Không xác định'
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'paid' => 'primary',
            default => 'secondary'
        };
    }

    public function canBeApproved()
    {
        return $this->status === 'pending' && Auth::user()->hasPermissionForModuleCode('expense_management', 'approve', 'branch');
    }

    public function canBeRejected()
    {
        return $this->status === 'pending' && Auth::user()->hasPermissionForModuleCode('expense_management', 'reject', 'branch');
    }
}
