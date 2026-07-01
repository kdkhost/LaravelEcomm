<?php

declare(strict_types=1);

namespace Modules\Cron\DTOs;

use Illuminate\Http\Request;
use Modules\Cron\Models\CronJob;

readonly class CronJobDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $command,
        public string $frequency,
        public ?array $params,
        public bool $is_active,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?CronJob $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            $id,
            $validated['name'] ?? $existing?->name ?? '',
            $validated['command'] ?? $existing?->command ?? '',
            $validated['frequency'] ?? $existing?->frequency ?? 'everyMinute',
            $validated['params'] ?? $existing?->params,
            isset($validated['is_active']) ? (bool) $validated['is_active'] : ($existing?->is_active ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'command' => $this->command,
            'frequency' => $this->frequency,
            'params' => $this->params,
            'is_active' => $this->is_active,
        ];
    }
}
