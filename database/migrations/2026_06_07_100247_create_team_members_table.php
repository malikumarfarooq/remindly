<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['admin', 'accountant', 'viewer'])->default('viewer');
            $table->enum('status', ['invited', 'active', 'suspended'])->default('invited');
            $table->string('invite_token', 64)->nullable()->unique();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->unique(['owner_user_id', 'member_user_id']);
            $table->index('owner_user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
