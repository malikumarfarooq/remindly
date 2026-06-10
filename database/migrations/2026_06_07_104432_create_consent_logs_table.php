<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('consent_type', [
                'terms_of_service',
                'privacy_policy',
                'marketing_emails',
                'sms_reminders',
                'data_processing'
            ]);
            $table->boolean('consented')->default(true);
            $table->string('version')->nullable();                // ToS version e.g. "v2.1"
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('consented_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'consent_type']);
            $table->index(['client_id', 'consent_type']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
    }
};
