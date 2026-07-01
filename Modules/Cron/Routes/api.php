<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cron\Http\Controllers\Api\CronController;

Route::get('cron/run', [CronController::class, 'runDue'])->name('api.cron.run');
Route::get('cron/health', [CronController::class, 'health'])->name('api.cron.health');
