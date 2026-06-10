<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reminder_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_sequence_id')->constrained()->cascadeOnDelete();
            $table->integer('step_number');
            $table->integer('days_offset');
            $table->string('offset_from')->default('due_date');   // due_date | issue_date
            $table->string('label');
            $table->enum('tone', ['friendly', 'professional', 'firm', 'final', 'demand'])->default('professional');
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'auto'])->default('auto');
            $table->string('subject_template')->nullable();
            $table->text('body_template')->nullable();
            $table->boolean('ai_generate')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('reminder_sequence_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reminder_steps');
        Schema::dropIfExists('reminder_sequences');
    }
};
