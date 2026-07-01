<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\PaymentProvider;

use Modules\Billing\Repository\PaymentProviderRepository;

class DeletePaymentProviderAction
{
    public function __construct(
        private readonly PaymentProviderRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
