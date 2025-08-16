<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    use HasUuids;

    protected $fillable = [
        'club_id',
        'name',
        'code',
        'description',
        'template_file_path',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($documentType) {
            if (empty($documentType->code)) {
                $documentType->code = self::generateCode();
            }
        });
    }

    public static function generateCode()
    {
        do {
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = "DT-{$randomNumber}";
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
