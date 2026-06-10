<?php

// ╔══════════════════════════════════════════════════════════════════╗
// ║        PayRemind — FINAL COMPLETE MIGRATION FILE                ║
// ║        Total: 45 Tables | International Production Standard     ║
// ║        Laravel 11.x | PHP 8.2+                                  ║
// ╚══════════════════════════════════════════════════════════════════╝
//
// RUN ORDER — copy each block into its own migration file:
//
//  BLOCK A — Laravel Infrastructure (5 tables)
//  BLOCK B — Core App (5 tables)
//  BLOCK C — Invoicing (3 tables)
//  BLOCK D — Reminders & AI (5 tables)
//  BLOCK E — Payments & Finance (5 tables)
//  BLOCK F — Security & Auth (5 tables)
//  BLOCK G — SaaS Billing (4 tables)
//  BLOCK H — Client Portal (2 tables)
//  BLOCK I — Compliance & GDPR (3 tables)
//  BLOCK J — Integrations & Webhooks (3 tables)
//  BLOCK K — Notifications & Misc (5 tables)
//
// ──────────────────────────────────────────────────────────────────

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ════════════════════════════════════════════════════════════════════
//  BLOCK A — LARAVEL INFRASTRUCTURE
// ════════════════════════════════════════════════════════════════════

// ── A1. SESSIONS
// File: 2024_01_01_000001_create_sessions_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

// ── A2. CACHE
// File: 2024_01_01_000002_create_cache_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};

// ── A3. JOBS & QUEUE
// File: 2024_01_01_000003_create_jobs_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }
};

// ── A4. PERSONAL ACCESS TOKENS (Laravel Sanctum)
// File: 2024_01_01_000004_create_personal_access_tokens_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};

// ── A5. PASSWORD RESET TOKENS
// File: 2024_01_01_000005_create_password_reset_tokens_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK B — CORE APP
// ════════════════════════════════════════════════════════════════════

// ── B1. USERS
// File: 2024_01_01_000010_create_users_table.php
return new class extends Migration {
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

            // SaaS Plan
            $table->string('plan')->default('free');              // free | pro | business
            $table->timestamp('plan_expires_at')->nullable();
            $table->string('stripe_customer_id')->nullable();     // Stripe customer ID
            $table->string('paddle_customer_id')->nullable();

            // Business Profile
            $table->string('payment_link_slug')->unique()->nullable();
            $table->string('tax_number')->nullable();             // VAT / GST number
            $table->string('website')->nullable();
            $table->text('business_address')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->nullable();             // ISO country code
            $table->string('postal_code')->nullable();
            $table->text('invoice_footer_notes')->nullable();     // default invoice footer
            $table->string('invoice_prefix')->default('INV');    // INV-001, PR-001

            // 2FA
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();

            // Meta
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
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

// ── B2. CLIENTS
// File: 2024_01_01_000011_create_clients_table.php
return new class extends Migration {
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

// ── B3. TAGS + PIVOT
// File: 2024_01_01_000012_create_tags_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#5f6368');
            $table->timestamps();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('client_tag', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['client_id', 'tag_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_tag');
        Schema::dropIfExists('tags');
    }
};

// ── B4. CURRENCY RATES
// File: 2024_01_01_000013_create_currency_rates_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('rate', 15, 6);
            $table->date('rate_date');
            $table->enum('source', ['api', 'manual'])->default('api');
            $table->timestamps();
            $table->unique(['base_currency', 'target_currency', 'rate_date']);
            $table->index('rate_date');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};

// ── B5. NOTIFICATION SETTINGS
// File: 2024_01_01_000014_create_notification_settings_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_reminders')->default(true);
            $table->boolean('whatsapp_reminders')->default(true);
            $table->boolean('sms_fallback')->default(false);
            $table->boolean('ai_tone_selection')->default(true);
            $table->boolean('optimal_send_time')->default(true);
            $table->boolean('auto_escalation')->default(false);
            $table->string('default_send_time', 5)->default('09:00');
            $table->string('default_send_day')->nullable();
            $table->string('default_language', 10)->default('en');
            $table->foreignId('default_sequence_id')->nullable()->constrained('reminder_sequences')->nullOnDelete();
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK C — INVOICING
// ════════════════════════════════════════════════════════════════════

// ── C1. INVOICE TEMPLATES
// File: 2024_01_01_000020_create_invoice_templates_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->integer('default_due_days')->default(14);
            $table->json('line_items');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_active']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};

// ── C2. INVOICES
// File: 2024_01_01_000021_create_invoices_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('invoice_templates')->nullOnDelete();
            $table->foreignId('recurring_invoice_id')->nullable()->constrained('recurring_invoices')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->enum('status', [
                'draft',
                'sent',
                'viewed',
                'pending',
                'partial',
                'paid',
                'overdue',
                'cancelled',
                'disputed'
            ])->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('viewed_at')->nullable();           // client opened payment link
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1); // rate at invoice time
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('tax_name')->nullable();               // "GST", "VAT", "Sales Tax"
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->enum('discount_type', ['percent', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('amount_outstanding', 15, 2)->default(0);
            $table->integer('days_overdue')->default(0);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('payment_link')->nullable();
            $table->string('payment_link_token', 64)->unique()->nullable();
            $table->integer('payment_link_views')->default(0);

            // AI
            $table->decimal('ai_risk_score', 5, 2)->nullable();
            $table->boolean('ai_escalated')->default(false);
            $table->string('ai_recommended_tone')->nullable();
            $table->timestamp('ai_risk_assessed_at')->nullable();

            // Reminder tracking
            $table->integer('reminder_step')->default(0);
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->timestamp('next_reminder_at')->nullable();
            $table->boolean('reminder_paused')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
            $table->index(['client_id', 'status']);
            $table->index('next_reminder_at');
            $table->index('payment_link_token');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

// ── C3. INVOICE LINE ITEMS
// File: 2024_01_01_000022_create_invoice_items_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->text('details')->nullable();                  // longer description
            $table->string('unit')->nullable();                   // "hrs", "pcs", "days"
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);       // line-level tax
            $table->decimal('discount_percent', 5, 2)->default(0); // line-level discount
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('invoice_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

// ── C4. RECURRING INVOICES
// File: 2024_01_01_000023_create_recurring_invoices_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('payment_due_days')->default(14);
            $table->json('line_items');
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->boolean('auto_send')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_at')->nullable();
            $table->date('next_generation_at')->nullable();
            $table->integer('invoices_generated')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_active']);
            $table->index('next_generation_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK D — REMINDERS & AI
// ════════════════════════════════════════════════════════════════════

// ── D1. REMINDER SEQUENCES
// File: 2024_01_01_000030_create_reminder_sequences_table.php
return new class extends Migration {
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

// ── D2. SENT REMINDERS LOG
// File: 2024_01_01_000031_create_reminders_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reminder_step_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('step_number');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->enum('tone', ['friendly', 'professional', 'firm', 'final', 'demand']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('recipient')->nullable();              // email or phone used
            $table->boolean('ai_generated')->default(false);
            $table->string('ai_model')->nullable();
            $table->enum('status', ['scheduled', 'sent', 'failed', 'cancelled'])->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_opened')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->string('external_message_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['user_id', 'sent_at']);
            $table->index('scheduled_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};

// ── D3. MESSAGE EVENTS (Webhook inbound logs)
// File: 2024_01_01_000032_create_message_events_table.php
return new class extends Migration {
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

// ── D4. AI INSIGHTS
// File: 2024_01_01_000033_create_ai_insights_table.php
return new class extends Migration {
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
            $table->index(['insightable_type', 'insightable_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};

// ── D5. AI LOGS
// File: 2024_01_01_000034_create_ai_logs_table.php
return new class extends Migration {
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


// ════════════════════════════════════════════════════════════════════
//  BLOCK E — PAYMENTS & FINANCE
// ════════════════════════════════════════════════════════════════════

// ── E1. PAYMENT GATEWAYS
// File: 2024_01_01_000040_create_payment_gateways_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->boolean('is_connected')->default(false);
            $table->boolean('is_default')->default(false);
            $table->json('credentials')->nullable();              // stored encrypted
            $table->json('settings')->nullable();
            $table->string('account_email')->nullable();          // connected account email
            $table->string('account_id')->nullable();             // gateway account ID
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'gateway']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};

// ── E2. BANK ACCOUNTS
// File: 2024_01_01_000041_create_bank_accounts_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');                     // stored encrypted
            $table->string('iban')->nullable();
            $table->string('swift_bic')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('country', 2)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('show_on_invoice')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};

// ── E3. PAYMENTS
// File: 2024_01_01_000042_create_payments_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('amount_in_base_currency', 15, 2)->nullable();
            $table->enum('method', [
                'online',
                'bank_transfer',
                'payment_link',
                'cash',
                'cheque',
                'stripe',
                'paypal',
                'jazzcash',
                'easypaisa',
                'manual'
            ]);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('gateway_name')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index('paid_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

// ── E4. PAYMENT ALLOCATIONS (Partial payments)
// File: 2024_01_01_000043_create_payment_allocations_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('allocated_amount', 15, 2);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('payment_id');
            $table->index('invoice_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};

// ── E5. REFUNDS
// File: 2024_01_01_000044_create_refunds_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('reason', [
                'duplicate',
                'fraudulent',
                'requested_by_customer',
                'service_not_delivered',
                'other'
            ]);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('gateway_refund_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'status']);
            $table->index('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};

// ── E6. PAYMENT DISPUTES (Chargebacks)
// File: 2024_01_01_000045_create_payment_disputes_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('dispute_id')->unique();               // gateway dispute ID
            $table->decimal('amount', 15, 2);
            $table->enum('status', [
                'warning_needs_response',
                'warning_under_review',
                'needs_response',
                'under_review',
                'won',
                'lost'
            ])->default('needs_response');
            $table->string('reason')->nullable();
            $table->timestamp('due_by')->nullable();              // respond by this date
            $table->json('evidence')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payment_disputes');
    }
};

// ── E7. DEMAND LETTERS
// File: 2024_01_01_000046_create_demand_letters_table.php
return new class extends Migration {
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


// ════════════════════════════════════════════════════════════════════
//  BLOCK F — SECURITY & AUTH
// ════════════════════════════════════════════════════════════════════

// ── F1. LOGIN ATTEMPTS (Brute force protection)
// File: 2024_01_01_000050_create_login_attempts_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address', 45);
            $table->boolean('was_successful')->default(false);
            $table->string('user_agent')->nullable();
            $table->string('country', 2)->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            $table->index(['email', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};

// ── F2. TWO FACTOR AUTH
// File: 2024_01_01_000051_create_two_factor_auth_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['totp', 'sms', 'email'])->default('totp');
            $table->string('secret')->nullable();                 // TOTP secret (encrypted)
            $table->json('recovery_codes')->nullable();           // backup codes (encrypted)
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('two_factor_auth');
    }
};

// ── F3. OAUTH CONNECTIONS (Social login)
// File: 2024_01_01_000052_create_oauth_connections_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('oauth_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['google', 'github', 'microsoft', 'linkedin']);
            $table->string('provider_user_id');
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->text('access_token')->nullable();             // encrypted
            $table->text('refresh_token')->nullable();            // encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_user_id']);
            $table->index('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('oauth_connections');
    }
};

// ── F4. ACTIVITY LOGS (Full audit trail)
// File: 2024_01_01_000053_create_activity_logs_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('loggable');
            $table->string('action');
            $table->string('description');
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['loggable_type', 'loggable_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

// ── F5. TEAM MEMBERS
// File: 2024_01_01_000054_create_team_members_table.php
return new class extends Migration {
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


// ════════════════════════════════════════════════════════════════════
//  BLOCK G — SAAS BILLING
// ════════════════════════════════════════════════════════════════════

// ── G1. PLANS
// File: 2024_01_01_000060_create_plans_table.php
return new class extends Migration {
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

// ── G2. SUBSCRIPTIONS
// File: 2024_01_01_000061_create_subscriptions_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', [
                'active',
                'trialing',
                'past_due',
                'cancelled',
                'expired',
                'paused'
            ])->default('trialing');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('current_period_end');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

// ── G3. SUBSCRIPTION INVOICES (PayRemind ki apni billing receipts)
// File: 2024_01_01_000062_create_subscription_invoices_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stripe_invoice_id')->nullable()->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'open', 'paid', 'void', 'uncollectible'])->default('paid');
            $table->string('receipt_url')->nullable();
            $table->string('hosted_invoice_url')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};

// ── G4. PROMO CODES
// File: 2024_01_01_000063_create_promo_codes_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 8, 2);                      // 20 = 20% or $20
            $table->integer('max_uses')->nullable();              // null = unlimited
            $table->integer('used_count')->default(0);
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });

        Schema::create('promo_code_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_applied', 10, 2);
            $table->timestamp('used_at')->useCurrent();
            $table->unique(['promo_code_id', 'user_id']);         // one use per user
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('promo_code_uses');
        Schema::dropIfExists('promo_codes');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK H — CLIENT PORTAL
// ════════════════════════════════════════════════════════════════════

// ── H1. CLIENT PORTAL SESSIONS
// File: 2024_01_01_000070_create_client_portal_sessions_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_portal_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // which account
            $table->string('token', 64)->unique();
            $table->enum('access_type', ['otp', 'magic_link', 'password'])->default('magic_link');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'expires_at']);
            $table->index('token');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_portal_sessions');
    }
};

// ── H2. CLIENT PORTAL OTP
// File: 2024_01_01_000071_create_client_portal_otps_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_portal_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('otp', 8);
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->string('recipient');
            $table->boolean('is_used')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['client_id', 'expires_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_portal_otps');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK I — COMPLIANCE & GDPR
// ════════════════════════════════════════════════════════════════════

// ── I1. EMAIL SUPPRESSIONS (Unsubscribe / Bounce list)
// File: 2024_01_01_000080_create_email_suppressions_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('phone')->nullable()->index();
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->enum('reason', ['unsubscribed', 'bounced', 'spam_complaint', 'manual'])->default('unsubscribed');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = global suppression
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('suppressed_at')->useCurrent();
            $table->timestamps();

            $table->index(['email', 'channel']);
            $table->index(['user_id', 'channel']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};

// ── I2. DATA DELETION REQUESTS (GDPR Right to be Forgotten)
// File: 2024_01_01_000081_create_data_deletion_requests_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->enum('type', ['user_account', 'client_data', 'full_export'])->default('user_account');
            $table->enum('status', ['pending', 'processing', 'completed', 'denied'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'status']);
            $table->index('status');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('data_deletion_requests');
    }
};

// ── I3. CONSENT LOGS (GDPR Consent Tracking)
// File: 2024_01_01_000082_create_consent_logs_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('consent_type', [
                'terms_of_service',
                'privacy_policy',
                'marketing_emails',
                'sms_reminders',
                'data_processing'
            ]);
            $table->boolean('consented')->default(true);
            $table->string('version')->nullable();                // ToS version e.g. "v2.1"
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('consented_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'consent_type']);
            $table->index(['client_id', 'consent_type']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK J — INTEGRATIONS & WEBHOOKS
// ════════════════════════════════════════════════════════════════════

// ── J1. WEBHOOK ENDPOINTS (User-defined outgoing webhooks)
// File: 2024_01_01_000090_create_webhook_endpoints_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('secret', 64)->nullable();             // for HMAC signature
            $table->json('events');                               // ['invoice.paid', 'reminder.sent']
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_active']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};

// ── J2. WEBHOOK DELIVERIES (Log of outgoing webhooks)
// File: 2024_01_01_000091_create_webhook_deliveries_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_endpoint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event');                              // invoice.paid
            $table->json('payload');
            $table->integer('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->boolean('was_successful')->default(false);
            $table->integer('attempts')->default(1);
            $table->timestamp('next_retry_at')->nullable();
            $table->decimal('duration_ms', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['webhook_endpoint_id', 'was_successful']);
            $table->index('next_retry_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};

// ── J3. INTEGRATION CONNECTIONS (Zapier, Slack, QuickBooks)
// File: 2024_01_01_000092_create_integration_connections_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('integration_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('integration', [
                'zapier',
                'slack',
                'quickbooks',
                'xero',
                'freshbooks',
                'hubspot',
                'google_sheets',
                'notion'
            ]);
            $table->boolean('is_active')->default(true);
            $table->json('credentials')->nullable();              // encrypted tokens
            $table->json('settings')->nullable();
            $table->string('workspace_name')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'integration']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('integration_connections');
    }
};


// ════════════════════════════════════════════════════════════════════
//  BLOCK K — NOTIFICATIONS & MISC
// ════════════════════════════════════════════════════════════════════

// ── K1. USER NOTIFICATIONS (In-app bell notifications)
// File: 2024_01_01_000100_create_user_notifications_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');                               // App\Notifications\InvoicePaid
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};

// ── K2. EMAIL TEMPLATES (Custom email branding)
// File: 2024_01_01_000101_create_email_templates_table.php
return new class extends Migration {
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

// ── K3. BRANDING SETTINGS (White-label / Custom branding)
// File: 2024_01_01_000102_create_branding_settings_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('branding_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#1a73e8');
            $table->string('secondary_color', 7)->default('#34a853');
            $table->string('font_family')->default('Inter');
            $table->string('invoice_template')->default('modern'); // modern | classic | minimal
            $table->string('custom_domain')->nullable();           // pay.yourbusiness.com
            $table->text('email_signature')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->boolean('show_payremind_branding')->default(true); // Pro: can hide
            $table->timestamps();
            $table->unique('user_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('branding_settings');
    }
};

// ── K4. REPORTS CACHE (Pre-computed analytics)
// File: 2024_01_01_000103_create_reports_cache_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('report_type');                        // monthly_revenue | risk_summary
            $table->string('period');                             // 2026-05 | 2026-Q1
            $table->json('data');
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['user_id', 'report_type', 'period']);
            $table->index('expires_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reports_cache');
    }
};

// ── K5. APP SETTINGS (Global admin settings)
// File: 2024_01_01_000104_create_app_settings_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');            // string | json | boolean | integer
            $table->string('group')->default('general');          // general | billing | ai | mail
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};

// ══════════════════════════════════════════════════════════════
//  END OF MIGRATIONS
//  Total Tables: 50
//  Created: 2026 | Laravel 11.x | PayRemind International
// ══════════════════════════════════════════════════════════════
