<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                               // Free, Pro, Business
            $table->string('slug')->unique();                     // free, pro, business
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->integer('max_clients')->default(5);           // -1 = unlimited
            $table->integer('max_invoices_per_month')->default(10);
            $table->integer('max_team_members')->default(1);
            $table->boolean('ai_features')->default(false);
            $table->boolean('whatsapp_reminders')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('recurring_invoices')->default(false);
            $table->json('features')->nullable();                 // full features list
            $table->string('stripe_price_id_monthly')->nullable();
            $table->string('stripe_price_id_yearly')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
