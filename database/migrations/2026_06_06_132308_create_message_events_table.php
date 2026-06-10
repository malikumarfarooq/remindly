<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('external_message_id')->nullable();
            $table->string('gateway');
            $table->enum('event_type', [
                'sent',
                'delivered',
                'opened',
                'clicked',
                'bounced',
                'spam',
                'unsubscribed',
                'failed'
            ]);
            $table->string('recipient')->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('url_clicked')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['reminder_id', 'event_type']);
            $table->index(['user_id', 'event_type']);
            $table->index('occurred_at');
            $table->index('external_message_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('message_events');
    }
};
