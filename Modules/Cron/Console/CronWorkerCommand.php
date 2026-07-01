<?php

declare(strict_types=1);

namespace Modules\Cron\Console;

use Illuminate\Console\Command;
use Modules\Cron\Models\CronWorkerStatus;
use Modules\Cron\Services\CronService;

class CronWorkerCommand extends Command
{
    protected $signature = 'cron:worker';
    protected $description = 'Internal cron worker that runs scheduled jobs continuously';

    public function handle(CronService $cronService): void
    {
        $this->info('Cron worker starting...');

        $status = CronWorkerStatus::getStatus();
        $status->update([
            'pid' => getmypid(),
            'is_running' => true,
            'started_at' => now(),
            'last_heartbeat' => now(),
        ]);

        $this->updateHeartbeat($status);

        while (true) {
            try {
                $count = $cronService->runDueJobs();
                if ($count > 0) {
                    $this->info("Executed {$count} due job(s).");
                }
            } catch (\Throwable $e) {
                $this->error('Worker error: ' . $e->getMessage());
            }

            $this->updateHeartbeat($status);
            sleep(30);
        }
    }

    private function updateHeartbeat(CronWorkerStatus $status): void
    {
        try {
            $status->update(['last_heartbeat' => now()]);
        } catch (\Throwable) {
        }
    }
}
