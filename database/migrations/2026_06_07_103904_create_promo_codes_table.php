<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
