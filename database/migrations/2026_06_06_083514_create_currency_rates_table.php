<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
