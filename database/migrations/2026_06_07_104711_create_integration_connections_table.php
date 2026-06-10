<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
