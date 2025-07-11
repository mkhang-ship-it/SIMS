<?php
// Migration: Create transactions table for payment recording system
// Features: UUID primary key, invoice relationship, multiple payment methods
// Payment tracking: amount, date, method (Sepay, PayOS, Cash), optional notes

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignUuid('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
            $table->decimal('paid_amount', 12, 2);
            $table->datetime('payment_date');
            $table->enum('payment_method', ['Sepay', 'PayOS', 'Cash']);
            $table->enum('type', ['income', 'outcome'])->default('income');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['invoice_id']);
            $table->index(['payment_date']);
            $table->index(['payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
