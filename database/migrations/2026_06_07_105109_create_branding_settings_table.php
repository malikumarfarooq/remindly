<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
