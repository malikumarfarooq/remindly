<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('phone')->nullable()->index();
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->enum('reason', ['unsubscribed', 'bounced', 'spam_complaint', 'manual'])->default('unsubscribed');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = global suppression
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('suppressed_at')->useCurrent();
            $table->timestamps();

            $table->index(['email', 'channel']);
            $table->index(['user_id', 'channel']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
