<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\PaymentProvider;

use Modules\Billing\DTOs\PaymentProviderDTO;
use Modules\Billing\Models\PaymentProvider;
use Modules\Billing\Repository\PaymentProviderRepository;

class CreatePaymentProviderAction
{
    public function __construct(
        private readonly PaymentProviderRepository $repository,
    ) {}

    public function execute(PaymentProviderDTO $dto): PaymentProvider
    {
        return $this->repository->create($dto->toArray());
    }
}
