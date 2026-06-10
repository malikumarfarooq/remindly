<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('loggable');
            $table->enum('action', [
                'email_generated',
                'risk_scored',
                'tone_selected',
                'send_time_predicted',
                'escalation_triggered',
                'demand_letter_drafted'
            ]);
            $table->string('model_used')->nullable();
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->decimal('cost_usd', 8, 6)->nullable();
            $table->boolean('was_used')->default(true);
            $table->timestamps();
            $table->index(['user_id', 'action']);
            $table->index('created_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
