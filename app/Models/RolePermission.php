<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use
    Illuminate\Database\Eloquent\Concerns\HasUuids;

class RolePermission extends Model
{
    public $incrementing = false; // Tắt tự động tăng ID

    protected $primaryKey = null; // Không dùng khóa chính

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public $timestamps = true; // Nếu không cần timestamps, đặt false
}
