<?php
// Updated ClassRoom model - Added longitude and latitude fields for location tracking
// Model structure includes name, code, status, longitude, and latitude fields
// Code is auto-generated using generateCode() method
// Longitude and latitude are optional string fields for flexible classroom location

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassRoom extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'code', 'status', 'longitude', 'latitude'];
}
