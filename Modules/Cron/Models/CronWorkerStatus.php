<?php

declare(strict_types=1);

namespace Modules\Cron\Models;

use Illuminate\Database\Eloquent\Model;

class CronWorkerStatus extends Model
{
    protected $table = 'cron_worker_status';

    protected $fillable = [
        'pid',
        'is_running',
        'started_at',
        'last_heartbeat',
    ];

    protected $casts = [
        'is_running' => 'boolean',
        'started_at' => 'datetime',
        'last_heartbeat' => 'datetime',
    ];

    public static function getStatus(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
