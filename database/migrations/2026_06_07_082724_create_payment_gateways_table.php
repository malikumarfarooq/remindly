<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->boolean('is_connected')->default(false);
            $table->boolean('is_default')->default(false);
            $table->json('credentials')->nullable();              // stored encrypted
            $table->json('settings')->nullable();
            $table->string('account_email')->nullable();          // connected account email
            $table->string('account_id')->nullable();             // gateway account ID
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'gateway']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
