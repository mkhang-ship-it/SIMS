<?php
// Migration for event_plan_budget_items table - Budget Cost Management for Event Plans
// Created: Budget cost management system linked to event_plans table
// Includes: content, quantity, unit, unit_price, responsible_person, notes

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
        Schema::create('event_plan_budget_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relationship with event_plans
            $table->foreignUuid('event_plan_id')->constrained('event_plans')->onDelete('cascade');

            // Budget item fields
            $table->text('content'); // Nội dung
            $table->integer('quantity'); // Số lượng
            $table->string('unit'); // Đơn vị
            $table->decimal('unit_price', 15, 2); // Giá tiền
            $table->string('responsible_person'); // Phụ trách
            $table->text('notes')->nullable(); // Ghi chú

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plan_budget_items');
    }
};
