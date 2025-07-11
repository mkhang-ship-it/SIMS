<?php
// Migration: Create invoices table for invoice management system
// Features: UUID primary key, auto-generated invoice codes, user/club/semester relationships
// Status: pending (default), paid, overdue with automatic status change logic
// Due date: automatically set to created_at + 7 days

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_code')->unique();
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('name');
            $table->decimal('total_amount', 12, 2);
            $table->datetime('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->foreignUuid('semester_id')->constrained('semesters');
            $table->foreignUuid('club_id')->constrained('clubs');
            $table->text('note')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['club_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['semester_id']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
