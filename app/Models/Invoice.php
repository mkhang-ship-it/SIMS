<?php
// Invoice Model - Invoice management system for club members
// Features: auto-generated invoice codes, relationship management, status tracking
// Auto-generates: invoice_code in format INV-{year}-{6 random numbers}
// Auto-sets: due_date = created_at + 7 days if not provided
// Status workflow: pending (default) -> paid/overdue
// Updated: Fixed remaining_amount calculation to prevent negative values

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_code',
        'user_id',
        'name',
        'total_amount',
        'due_date',
        'status',
        'semester_id',
        'club_id',
        'note'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate invoice code and set due_date
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_code)) {
                $invoice->invoice_code = self::generateInvoiceCode();
            }
            if (empty($invoice->due_date)) {
                $invoice->due_date = Carbon::now()->addDays(7);
            }
        });
    }

    /**
     * Generate unique invoice code in format INV-{year}-{6 random numbers}
     */
    public static function generateInvoiceCode()
    {
        do {
            $year = date('Y');
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $invoiceCode = "INV-{$year}-{$randomNumber}";
        } while (self::where('invoice_code', $invoiceCode)->exists());

        return $invoiceCode;
    }

    /**
     * Get the user this invoice belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the club this invoice belongs to
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the semester this invoice belongs to
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get all transactions for this invoice
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Chờ Thanh Toán',
            'paid' => 'Đã Thanh Toán',
            'overdue' => 'Quá Hạn'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'pending' && Carbon::now()->isAfter($this->due_date);
    }

    /**
     * Check if invoice can be edited (only pending status)
     */
    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if invoice can be deleted (only pending status)
     */
    public function canBeDeleted()
    {
        return $this->status === 'pending';
    }

    /**
     * Get total paid amount from transactions
     */
    public function getTotalPaidAttribute()
    {
        return $this->transactions->sum('paid_amount');
    }

    /**
     * Get remaining amount to be paid
     */
    public function getRemainingAmountAttribute()
    {
        $remaining = $this->total_amount - $this->total_paid;
        return max(0, $remaining); // Đảm bảo không trả về giá trị âm
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid()
    {
        return $this->total_paid >= $this->total_amount;
    }

    /**
     * Automatically mark as paid if fully paid
     */
    public function updateStatusIfFullyPaid()
    {
        if ($this->isFullyPaid() && $this->status !== 'paid') {
            $this->update(['status' => 'paid']);
        }
    }

    /**
     * Scope for pending invoices that are overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', Carbon::now());
    }

    /**
     * Scope for pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for invoices by club
     */
    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }
}
