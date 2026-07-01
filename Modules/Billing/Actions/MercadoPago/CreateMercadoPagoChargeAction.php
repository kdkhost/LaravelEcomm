<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\MercadoPago;

use Modules\Billing\DTOs\MercadoPagoDTO;
use Modules\Billing\Services\MercadoPagoService;

readonly class CreateMercadoPagoChargeAction
{
    public function __construct(
        private MercadoPagoService $service,
    ) {}

    public function execute(MercadoPagoDTO $dto): ?array
    {
        return $this->service->createPreference(
            items: $dto->items,
            payer: $dto->payer,
            returnUrl: $dto->returnUrl,
            webhookUrl: $dto->webhookUrl,
        );
    }
}
