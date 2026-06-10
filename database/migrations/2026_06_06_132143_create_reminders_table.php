<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reminder_step_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('step_number');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->enum('tone', ['friendly', 'professional', 'firm', 'final', 'demand']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('recipient')->nullable();              // email or phone used
            $table->boolean('ai_generated')->default(false);
            $table->string('ai_model')->nullable();
            $table->enum('status', ['scheduled', 'sent', 'failed', 'cancelled'])->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_opened')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->string('external_message_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['user_id', 'sent_at']);
            $table->index('scheduled_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
