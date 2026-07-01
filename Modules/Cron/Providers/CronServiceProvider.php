<?php

declare(strict_types=1);

namespace Modules\Cron\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Cron\Console\CronWorkerCommand;

class CronServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Cron', 'Database/Migrations'));

        if ($this->app->runningInConsole()) {
            $this->commands([CronWorkerCommand::class]);
        }
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerViews(): void
    {
        $sourcePath = module_path('Cron', 'Resources/views');
        $this->loadViewsFrom([$sourcePath], 'cron');
    }

    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/cron');
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'cron');
        } else {
            $this->loadTranslationsFrom(module_path('Cron', 'Resources/lang'), 'cron');
        }
    }
}
