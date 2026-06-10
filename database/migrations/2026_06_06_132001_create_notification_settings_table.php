<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_reminders')->default(true);
            $table->boolean('whatsapp_reminders')->default(true);
            $table->boolean('sms_fallback')->default(false);
            $table->boolean('ai_tone_selection')->default(true);
            $table->boolean('optimal_send_time')->default(true);
            $table->boolean('auto_escalation')->default(false);
            $table->string('default_send_time', 5)->default('09:00');
            $table->string('default_send_day')->nullable();
            $table->string('default_language', 10)->default('en');
            $table->foreignId('default_sequence_id')->nullable()->constrained('reminder_sequences')->nullOnDelete();
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
