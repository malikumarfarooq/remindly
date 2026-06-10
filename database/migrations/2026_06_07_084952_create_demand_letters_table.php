<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demand_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['drafted', 'reviewed', 'sent', 'legal_escalated'])->default('drafted');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('ai_generated')->default(true);
            $table->string('ai_model')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('sent_via', ['email', 'courier', 'legal', 'whatsapp'])->nullable();
            $table->text('legal_notes')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('demand_letters');
    }
};
