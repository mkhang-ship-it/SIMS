<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Slot extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'start_time', 'end_time', 'status'];
}
