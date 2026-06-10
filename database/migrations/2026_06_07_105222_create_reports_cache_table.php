<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
