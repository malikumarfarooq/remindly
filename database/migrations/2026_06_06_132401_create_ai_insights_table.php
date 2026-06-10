<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('insightable');
            $table->enum('type', [
                'risk_alert',
                'payment_prediction',
                'tone_suggestion',
                'send_time',
                'escalation',
                'collection_tip'
            ]);
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->text('message');
            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_dismissed']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
