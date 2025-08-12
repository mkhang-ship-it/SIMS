<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Setting extends Model
{
    use HasUuids;

    protected $fillable = ['club_id', 'key', 'value', 'is_admin'];

    /**
     * Lấy value theo club_id và key.
     */
    public static function getValue($clubId, $key)
    {
        $query = self::where('key', $key);

        if ($clubId === null) {
            // Admin settings
            $query->where('is_admin', 'central');
        } else {
            // Club settings
            $query->where('club_id', $clubId)->where('is_admin', 'branch');
        }

        return optional($query->first())->value;
    }

    /**
     * Lấy nhiều value theo club_id và danh sách keys (trả về mảng key => value).
     */
    public static function getMany($clubId, array $keys)
    {
        $query = self::whereIn('key', $keys);

        if ($clubId === null) {
            // Admin settings
            $query->where('is_admin', 'central');
        } else {
            // Club settings
            $query->where('club_id', $clubId)->where('is_admin', 'branch');
        }

        return $query->pluck('value', 'key')->toArray();
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
     * Lấy cấu hình background image cho trang ngoài.
     */
    public static function getExternalBackgroundSettings()
    {
        $backgroundKeys = [
            'external_bg_image',
            'external_bg_overlay_color',
            'external_bg_overlay_opacity'
        ];

        return self::getMany(null, $backgroundKeys);
    }

    /**
     * Quan hệ ngược về Club (nếu có model Club).
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
