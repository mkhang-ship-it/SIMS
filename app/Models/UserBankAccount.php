<?php
// Đã cập nhật: Thêm fillable fields, relationship với User model và các thuộc tính cần thiết cho UserBankAccount. Sử dụng UUID cho primary key.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserBankAccount extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_code',
        'bank_bin',
        'bank_short_name',
        'bank_swift_code',
        'bank_account_name',
        'bank_account_number',
    ];

    /**
     * Get the user that owns the bank account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
