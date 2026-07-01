<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;
use Modules\Billing\Models\PaymentProvider;

readonly class PaymentProviderDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $public_key,
        public ?string $secret_key,
        public int $status,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?PaymentProvider $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            $id,
            $validated['name'] ?? $existing?->name ?? '',
            $validated['public_key'] ?? $existing?->public_key,
            $validated['secret_key'] ?? $existing?->secret_key,
            isset($validated['status']) ? (int) $validated['status'] : $existing?->status ?? 0,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'public_key' => $this->public_key,
            'secret_key' => $this->secret_key,
            'status' => $this->status,
        ], fn ($value) => $value !== null);
    }
}
