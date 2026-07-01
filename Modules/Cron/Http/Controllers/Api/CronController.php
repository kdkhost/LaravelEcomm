<?php

declare(strict_types=1);

namespace Modules\Cron\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Cron\Models\CronWorkerStatus;
use Modules\Cron\Services\CronService;

class CronController extends CoreController
{
    public function __construct(
        private readonly CronService $cronService,
    ) {}

    public function runDue(): JsonResponse
    {
        $count = $this->cronService->runDueJobs();

        return response()->json([
            'success' => true,
            'executed' => $count,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function health(): JsonResponse
    {
        $status = CronWorkerStatus::getStatus();

        return response()->json([
            'running' => $status->is_running,
            'pid' => $status->pid,
            'started_at' => $status->started_at?->toIso8601String(),
            'last_heartbeat' => $status->last_heartbeat?->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
