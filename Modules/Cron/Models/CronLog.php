<?php

declare(strict_types=1);

namespace Modules\Cron\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronLog extends Model
{
    protected $table = 'cron_logs';

    protected $fillable = [
        'cron_job_id',
        'started_at',
        'finished_at',
        'duration',
        'status',
        'output',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration' => 'float',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(CronJob::class, 'cron_job_id');
    }
}
