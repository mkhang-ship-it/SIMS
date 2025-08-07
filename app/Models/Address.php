<?php
// Updated: Added support for club addresses by adding club_id to fillable and club relationship
// Updated user_id to be nullable since addresses can now belong to either users or clubs

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Address extends Model
{
    use HasUuids;

    protected $fillable = ['user_id', 'club_id', 'province_id', 'commune_id', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    // Fixed syntax error in getFullAddressAttribute and added null checks for commune and province
    public function getFullAddressAttribute()
    {
        $address = $this->address ?? '';
        $commune = $this->commune ? $this->commune->name : '';
        $province = $this->province ? $this->province->name : '';

        $parts = array_filter([$address, $commune, $province]);
        return implode(', ', $parts);
    }
}
