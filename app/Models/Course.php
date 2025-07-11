<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Course extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
    ];
}
