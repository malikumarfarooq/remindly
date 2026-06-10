<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('secret', 64)->nullable();             // for HMAC signature
            $table->json('events');                               // ['invoice.paid', 'reminder.sent']
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_active']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
