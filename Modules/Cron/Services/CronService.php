<?php

declare(strict_types=1);

namespace Modules\Cron\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Cron\Models\CronJob;
use Modules\Cron\Models\CronLog;
use Modules\Cron\Models\CronWorkerStatus;
use Symfony\Component\Process\Process;

class CronService
{
    public function runDueJobs(): int
    {
        $jobs = CronJob::due()->get();
        $count = 0;

        foreach ($jobs as $job) {
            $this->runJob($job);
            $count++;
        }

        return $count;
    }

    public function runJob(CronJob $job): string
    {
        $start = microtime(true);
        $log = CronLog::create([
            'cron_job_id' => $job->id,
            'started_at' => now(),
            'status' => 'running',
        ]);

        try {
            $exitCode = Artisan::call($job->command, $job->params ?? []);
            $output = Artisan::output();
            $duration = microtime(true) - $start;

            $status = $exitCode === 0 ? 'success' : 'failed';

            $log->update([
                'finished_at' => now(),
                'duration' => round($duration, 4),
                'status' => $status,
                'output' => $output,
            ]);

            $job->update([
                'last_run_at' => now(),
                'next_run_at' => now()->addSeconds(CronJob::getFrequencyInterval($job->frequency)),
                'status' => $status,
                'last_output' => $output,
            ]);

            return $status;
        } catch (\Throwable $e) {
            $duration = microtime(true) - $start;

            $log->update([
                'finished_at' => now(),
                'duration' => round($duration, 4),
                'status' => 'error',
                'output' => $e->getMessage(),
            ]);

            $job->update([
                'last_run_at' => now(),
                'next_run_at' => now()->addSeconds(CronJob::getFrequencyInterval($job->frequency)),
                'status' => 'error',
                'last_output' => $e->getMessage(),
            ]);

            Log::error('CronJob failed: ' . $job->name . ' - ' . $e->getMessage());

            return 'error';
        }
    }

    public function startWorker(): bool
    {
        $status = CronWorkerStatus::getStatus();
        if ($status->is_running && $this->isProcessAlive($status->pid)) {
            return false;
        }

        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');
        $logFile = storage_path('logs/cron-worker.log');

        $cmd = "\"$phpBinary\" \"$artisan\" cron:worker > \"$logFile\" 2>&1 &";

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = "start /B \"CronWorker\" \"$phpBinary\" \"$artisan\" cron:worker";
            $process = proc_open($cmd, [], $pipes);
            if (is_resource($process)) {
                $procInfo = proc_get_status($process);
                proc_close($process);
            }
        } else {
            exec($cmd . ' echo $!', $output);
            $pid = $output[0] ?? null;

            $status->update([
                'pid' => $pid,
                'is_running' => true,
                'started_at' => now(),
                'last_heartbeat' => now(),
            ]);
        }

        return true;
    }

    public function stopWorker(): bool
    {
        $status = CronWorkerStatus::getStatus();
        if (!$status->is_running || !$status->pid) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec("taskkill /F /PID {$status->pid} 2>NUL", $output, $exitCode);
        } else {
            exec("kill {$status->pid} 2>/dev/null", $output, $exitCode);
        }

        $status->update([
            'is_running' => false,
            'pid' => null,
        ]);

        return true;
    }

    public function isWorkerRunning(): bool
    {
        $status = CronWorkerStatus::getStatus();
        if (!$status->is_running) {
            return false;
        }

        if (!$this->isProcessAlive($status->pid)) {
            $status->update(['is_running' => false, 'pid' => null]);
            return false;
        }

        return true;
    }

    private function isProcessAlive(?string $pid): bool
    {
        if (!$pid) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec("tasklist /NH /FI \"PID eq $pid\" 2>NUL", $output);
            return count($output) > 0 && str_contains($output[0] ?? '', (string) $pid);
        }

        exec("kill -0 $pid 2>/dev/null", $output, $exitCode);
        return $exitCode === 0;
    }
}
