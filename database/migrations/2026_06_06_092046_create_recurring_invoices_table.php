<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('payment_due_days')->default(14);
            $table->json('line_items');
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->boolean('auto_send')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_at')->nullable();
            $table->date('next_generation_at')->nullable();
            $table->integer('invoices_generated')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_active']);
            $table->index('next_generation_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
