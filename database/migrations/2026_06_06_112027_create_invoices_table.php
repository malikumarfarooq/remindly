<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('invoice_templates')->nullOnDelete();
            $table->foreignId('recurring_invoice_id')->nullable()->constrained('recurring_invoices')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->enum('status', [
                'draft',
                'sent',
                'viewed',
                'pending',
                'partial',
                'paid',
                'overdue',
                'cancelled',
                'disputed'
            ])->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('viewed_at')->nullable();           // client opened payment link
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1); // rate at invoice time
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('tax_name')->nullable();               // "GST", "VAT", "Sales Tax"
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->enum('discount_type', ['percent', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('amount_outstanding', 15, 2)->default(0);
            $table->integer('days_overdue')->default(0);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('payment_link')->nullable();
            $table->string('payment_link_token', 64)->unique()->nullable();
            $table->integer('payment_link_views')->default(0);

            // AI
            $table->decimal('ai_risk_score', 5, 2)->nullable();
            $table->boolean('ai_escalated')->default(false);
            $table->string('ai_recommended_tone')->nullable();
            $table->timestamp('ai_risk_assessed_at')->nullable();

            // Reminder tracking
            $table->integer('reminder_step')->default(0);
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->timestamp('next_reminder_at')->nullable();
            $table->boolean('reminder_paused')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
            $table->index(['client_id', 'status']);
            $table->index('next_reminder_at');
            $table->index('payment_link_token');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
