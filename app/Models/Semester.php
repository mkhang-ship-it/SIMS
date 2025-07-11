<?php
// Updated Semester model - Added comment as per user requirements
// Model for managing semesters with fillable fields: name, start_date, end_date, status
// Added invoices() relationship to manage semester-specific invoices

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Semester extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'start_date', 'end_date', 'status'];

    /**
     * Get all invoices for this semester
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
