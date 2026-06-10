<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('language', 10)->default('en');
            $table->enum('preferred_channel', ['email', 'sms', 'whatsapp', 'auto'])->default('auto');
            $table->string('currency', 3)->default('USD');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('website')->nullable();

            // AI & Risk
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->decimal('risk_score', 5, 2)->default(0);
            $table->integer('avg_days_late')->default(0);
            $table->integer('total_invoices_count')->default(0);
            $table->decimal('total_invoiced', 15, 2)->default(0);
            $table->decimal('total_outstanding', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('member_since')->nullable();
            $table->string('preferred_send_time', 5)->nullable();  // "09:00"
            $table->string('preferred_send_day')->nullable();       // "Tuesday"
            $table->boolean('email_unsubscribed')->default(false);
            $table->boolean('sms_unsubscribed')->default(false);
            $table->boolean('whatsapp_unsubscribed')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'risk_level']);
            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
