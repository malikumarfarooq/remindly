<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('reason', [
                'duplicate',
                'fraudulent',
                'requested_by_customer',
                'service_not_delivered',
                'other'
            ]);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('gateway_refund_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'status']);
            $table->index('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
