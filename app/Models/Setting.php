<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Setting extends Model
{
    use HasUuids;

    protected $fillable = ['club_id', 'key', 'value'];

    /**
     * Lấy value theo club_id và key.
     */
    public static function getValue($clubId, $key)
    {
        return optional(
            self::where('club_id', $clubId)
                ->where('key', $key)
                ->first()
        )->value;
    }

    /**
     * Lấy nhiều value theo club_id và danh sách keys (trả về mảng key => value).
     */
    public static function getMany($clubId, array $keys)
    {
        return self::where('club_id', $clubId)
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Lấy thông tin ngân hàng theo club_id.
     */
    public static function getBankInfo($clubId)
    {
        $bankKeys = [
            'bank_name',
            'bank_code',
            'bank_bin',
            'bank_short_name',
            'bank_swift_code',
            'bank_account_name',
            'bank_account_number'
        ];

        return self::getMany($clubId, $bankKeys);
    }

    /**
     * Quan hệ ngược về Club (nếu có model Club).
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
