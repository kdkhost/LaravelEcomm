<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\PaymentProvider;

use Modules\Billing\Models\PaymentProvider;
use Modules\Billing\Repository\PaymentProviderRepository;

class FindPaymentProviderAction
{
    public function __construct(
        private readonly PaymentProviderRepository $repository,
    ) {}

    public function execute(int $id): PaymentProvider
    {
        return $this->repository->findById($id);
    }
}
