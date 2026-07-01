<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class CentralCronWorker extends Command
{
    protected $signature = 'system:cron-work
                            {--once : Executa apenas um ciclo e encerra}
                            {--sleep=5 : Segundos de espera entre verificacoes}
                            {--max-runtime=0 : Tempo maximo em segundos; 0 deixa rodando sem limite}
                            {--without-queue : Nao processa a fila depois do agendador}';

    protected $description = 'Executa o agendador e a fila em um worker central, sem depender de visitas ao site.';

    public function handle(): int
    {
        date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));

        $lock = $this->openLock();
        if (! $lock) {
            return Command::FAILURE;
        }

        if (! flock($lock, LOCK_EX | LOCK_NB)) {
            $this->error('Ja existe um worker central de cron em execucao.');
            fclose($lock);

            return Command::FAILURE;
        }

        $sleep = max(1, min(60, (int) $this->option('sleep')));
        $maxRuntime = max(0, (int) $this->option('max-runtime'));
        $startedAt = time();
        $lastMinute = null;

        $this->info('Worker central iniciado em '.now()->format('d/m/Y H:i:s').' ('.config('app.timezone').').');

        try {
            do {
                $currentMinute = now()->format('Y-m-d H:i');

                if ($currentMinute !== $lastMinute) {
                    $lastMinute = $currentMinute;
                    $this->runSchedule();
                    $this->runQueue();
                }

                if ($this->option('once')) {
                    break;
                }

                if ($maxRuntime > 0 && (time() - $startedAt) >= $maxRuntime) {
                    $this->info('Tempo maximo atingido. Encerrando worker central.');
                    break;
                }

                sleep($sleep);
            } while (true);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return Command::SUCCESS;
    }

    /**
     * @return resource|null
     */
    private function openLock()
    {
        $lockPath = storage_path('framework/central-cron-worker.lock');
        $directory = dirname($lockPath);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->error('Nao foi possivel criar o diretorio de lock do worker central.');

            return null;
        }

        $lock = fopen($lockPath, 'c');
        if (! $lock) {
            $this->error('Nao foi possivel abrir o arquivo de lock do worker central.');

            return null;
        }

        return $lock;
    }

    private function runSchedule(): void
    {
        $this->line('['.now()->format('d/m/Y H:i:s').'] Executando agendador central...');

        try {
            Artisan::call('schedule:run');
            $output = trim(Artisan::output());

            if ($output !== '') {
                $this->line($output);
            }
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Falha no agendador: '.$exception->getMessage());
        }
    }

    private function runQueue(): void
    {
        if ($this->option('without-queue') || config('queue.default') === 'sync') {
            return;
        }

        try {
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--tries' => 3,
                '--timeout' => 60,
            ]);

            $output = trim(Artisan::output());
            if ($output !== '') {
                $this->line($output);
            }
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Falha ao processar fila: '.$exception->getMessage());
        }
    }
}
