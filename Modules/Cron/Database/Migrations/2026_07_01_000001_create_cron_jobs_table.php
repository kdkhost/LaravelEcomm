<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('command');
            $table->string('frequency')->default('everyMinute');
            $table->text('params')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->string('status')->default('idle');
            $table->text('last_output')->nullable();
            $table->timestamps();
        });

        Schema::create('cron_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cron_job_id')->constrained('cron_jobs')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->float('duration')->nullable();
            $table->string('status');
            $table->text('output')->nullable();
            $table->timestamps();
        });

        Schema::create('cron_worker_status', function (Blueprint $table): void {
            $table->id();
            $table->string('pid')->nullable();
            $table->boolean('is_running')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
        Schema::dropIfExists('cron_jobs');
        Schema::dropIfExists('cron_worker_status');
    }
};
