<?php

declare(strict_types=1);

namespace Modules\Cron\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Cron\Models\CronLog;
use Modules\Cron\Services\CronService;

class CronController extends CoreController
{
    public function __construct(
        private readonly CronService $cronService,
    ) {}

    public function logs(): View
    {
        return view('cron::cron-jobs.logs', [
            'logs' => CronLog::with('job')->orderByDesc('id')->paginate(50),
        ]);
    }

    public function startWorker(): JsonResponse
    {
        $started = $this->cronService->startWorker();

        return response()->json([
            'success' => $started,
            'message' => $started ? __('cron::messages.worker_started') : __('cron::messages.worker_already_running'),
        ]);
    }

    public function stopWorker(): JsonResponse
    {
        $stopped = $this->cronService->stopWorker();

        return response()->json([
            'success' => $stopped,
            'message' => $stopped ? __('cron::messages.worker_stopped') : __('cron::messages.worker_not_running'),
        ]);
    }

    public function workerStatus(): JsonResponse
    {
        return response()->json([
            'running' => $this->cronService->isWorkerRunning(),
        ]);
    }
}
