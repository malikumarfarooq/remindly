<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('dispute_id')->unique();               // gateway dispute ID
            $table->decimal('amount', 15, 2);
            $table->enum('status', [
                'warning_needs_response',
                'warning_under_review',
                'needs_response',
                'under_review',
                'won',
                'lost'
            ])->default('needs_response');
            $table->string('reason')->nullable();
            $table->timestamp('due_by')->nullable();              // respond by this date
            $table->json('evidence')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_disputes');
    }
};
