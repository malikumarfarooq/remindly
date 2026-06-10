<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
