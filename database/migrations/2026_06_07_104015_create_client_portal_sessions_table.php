<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // which account
            $table->string('token', 64)->unique();
            $table->enum('access_type', ['otp', 'magic_link', 'password'])->default('magic_link');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'expires_at']);
            $table->index('token');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_portal_sessions');
    }
};
