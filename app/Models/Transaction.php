<?php
// Transaction Model - Payment recording system for invoices and expenses
// Features: UUID primary key, invoice/expense relationships, payment tracking
// Tracks: payment amount, date, method (Sepay, PayOS, Cash), type (income/outcome), optional notes
// Note: All invoice payment transactions are automatically set to 'income' type
// Note: invoice_id is nullable to support expense transactions
// Automatically updates invoice status when payments are recorded

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_id',
        'expense_id',
        'type',
        'paid_amount',
        'payment_date',
        'payment_method',
        'note'
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to update invoice status after transaction
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            if ($transaction->isForInvoice() && $transaction->invoice) {
                $transaction->invoice->updateStatusIfFullyPaid();
            }
        });

        static::updated(function ($transaction) {
            if ($transaction->isForInvoice() && $transaction->invoice) {
                $transaction->invoice->updateStatusIfFullyPaid();
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->isForInvoice() && $transaction->invoice) {
                $transaction->invoice->updateStatusIfFullyPaid();
            }
        });
    }

    /**
     * Get the invoice this transaction belongs to
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the expense this transaction belongs to
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Get type label in Vietnamese
     */
    public function getTypeLabelAttribute()
    {
        $types = [
            'income' => 'Thu',
            'outcome' => 'Chi'
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get type color for UI
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'income' => 'success',
            'outcome' => 'danger'
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Get payment method label in Vietnamese
     */
    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'Sepay' => 'Sepay',
            'PayOS' => 'PayOS',
            'Cash' => 'Tiền Mặt'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get payment method color for UI
     */
    public function getPaymentMethodColorAttribute()
    {
        $colors = [
            'Sepay' => 'primary',
            'PayOS' => 'info',
            'Paykit' => 'warning',
            'Cash' => 'success'
        ];

        return $colors[$this->payment_method] ?? 'secondary';
    }

    /**
     * Format payment date
     */
    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date->format('d/m/Y H:i');
    }

    /**
     * Scope for transactions by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for income transactions
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for outcome transactions
     */
    public function scopeOutcome($query)
    {
        return $query->where('type', 'outcome');
    }

    /**
     * Scope for transactions by payment method
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for transactions by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope for transactions by invoice
     */
    public function scopeByInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /**
     * Scope for transactions by expense
     */
    public function scopeByExpense($query, $expenseId)
    {
        return $query->where('expense_id', $expenseId);
    }

    /**
     * Check if transaction is for an invoice
     */
    public function isForInvoice()
    {
        return !is_null($this->invoice_id);
    }

    /**
     * Check if transaction is for an expense
     */
    public function isForExpense()
    {
        return !is_null($this->expense_id);
    }

    /**
     * Get the related entity (invoice or expense)
     */
    public function getRelatedEntityAttribute()
    {
        if ($this->isForInvoice()) {
            return $this->invoice;
        }
        if ($this->isForExpense()) {
            return $this->expense;
        }
        return null;
    }
}
