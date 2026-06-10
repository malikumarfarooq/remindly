<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', [
                'reminder_friendly',
                'reminder_firm',
                'reminder_final',
                'invoice_sent',
                'payment_received',
                'demand_letter'
            ]);
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();                // plain text fallback
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type', 'is_active']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
