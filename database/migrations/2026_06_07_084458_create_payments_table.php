<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('amount_in_base_currency', 15, 2)->nullable();
            $table->enum('method', [
                'online',
                'bank_transfer',
                'payment_link',
                'cash',
                'cheque',
                'stripe',
                'paypal',
                'jazzcash',
                'easypaisa',
                'manual'
            ]);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('gateway_name')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index('paid_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
