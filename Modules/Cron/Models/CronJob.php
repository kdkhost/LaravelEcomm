<?php

declare(strict_types=1);

namespace Modules\Cron\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CronJob extends Model
{
    protected $table = 'cron_jobs';

    protected $fillable = [
        'name',
        'command',
        'frequency',
        'params',
        'is_active',
        'last_run_at',
        'next_run_at',
        'status',
        'last_output',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'params' => 'array',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(CronLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            });
    }

    public static function getFrequencyInterval(string $frequency): int
    {
        return match ($frequency) {
            'everyMinute' => 60,
            'everyFiveMinutes' => 300,
            'everyTenMinutes' => 600,
            'everyFifteenMinutes' => 900,
            'everyThirtyMinutes' => 1800,
            'hourly' => 3600,
            'everyTwoHours' => 7200,
            'everyThreeHours' => 10800,
            'everyFourHours' => 14400,
            'everySixHours' => 21600,
            'daily' => 86400,
            'weekly' => 604800,
            default => 3600,
        };
    }
}
