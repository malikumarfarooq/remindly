<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['totp', 'sms', 'email'])->default('totp');
            $table->string('secret')->nullable();                 // TOTP secret (encrypted)
            $table->json('recovery_codes')->nullable();           // backup codes (encrypted)
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('two_factor_auth');
    }
};
