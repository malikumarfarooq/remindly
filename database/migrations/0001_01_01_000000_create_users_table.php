<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('company_name')->nullable();
            $table->enum('business_type', ['freelancer', 'agency', 'company', 'individual'])->default('freelancer');
            $table->string('avatar')->nullable();
            $table->string('timezone')->default('Asia/Karachi');
            $table->string('currency', 3)->default('USD');
            $table->string('language', 10)->default('en');
            $table->string('plan')->default('free');
            $table->timestamp('plan_expires_at')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('paddle_customer_id')->nullable();
            $table->string('payment_link_slug')->unique()->nullable();
            $table->string('tax_number')->nullable();
            $table->string('website')->nullable();
            $table->text('business_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->text('invoice_footer_notes')->nullable();
            $table->string('invoice_prefix')->default('INV');
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->string('referral_code')->unique()->nullable();
            $table->string('referred_by')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_suspended')->default(false);
            $table->text('suspension_reason')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['plan', 'is_active']);
            $table->index('country');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
