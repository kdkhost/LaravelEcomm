<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercadopago_webhook_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type')->nullable()->index();
            $table->string('resource_id')->nullable()->index();
            $table->string('external_reference')->nullable()->index();
            $table->string('status')->default('received')->index();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercadopago_webhook_logs');
    }
};
