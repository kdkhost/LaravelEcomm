<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class MercadoPagoDTO
{
    public function __construct(
        public ?float $amount,
        public ?string $currency = 'BRL',
        public ?string $description = null,
        public ?string $returnUrl = null,
        public ?string $webhookUrl = null,
        public ?array $items = [],
        public ?array $payer = [],
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            currency: $data['currency'] ?? 'BRL',
            description: $data['description'] ?? 'Loja Rataplam',
            returnUrl: $data['returnUrl'] ?? route('mercadopago.success'),
            webhookUrl: $data['webhookUrl'] ?? route('mercadopago.webhook'),
            items: $data['items'] ?? [],
            payer: $data['payer'] ?? [],
        );
    }
}
