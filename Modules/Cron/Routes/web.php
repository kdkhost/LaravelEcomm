<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cron\Http\Controllers\CronJobController;
use Modules\Cron\Http\Controllers\CronController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function (): void {
    Route::resource('cron-jobs', CronJobController::class)->names([
        'index' => 'admin.cron-jobs.index',
        'create' => 'admin.cron-jobs.create',
        'store' => 'admin.cron-jobs.store',
        'edit' => 'admin.cron-jobs.edit',
        'update' => 'admin.cron-jobs.update',
        'destroy' => 'admin.cron-jobs.destroy',
    ]);

    Route::get('cron-jobs/{cronJob}/run', [CronJobController::class, 'run'])->name('admin.cron-jobs.run');
    Route::get('cron/logs', [CronController::class, 'logs'])->name('admin.cron.logs');
    Route::post('cron/worker/start', [CronController::class, 'startWorker'])->name('admin.cron.worker.start');
    Route::post('cron/worker/stop', [CronController::class, 'stopWorker'])->name('admin.cron.worker.stop');
    Route::get('cron/worker/status', [CronController::class, 'workerStatus'])->name('admin.cron.worker.status');
});
