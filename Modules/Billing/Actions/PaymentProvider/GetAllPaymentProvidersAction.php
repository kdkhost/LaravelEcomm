<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\PaymentProvider;

use Illuminate\Database\Eloquent\Collection;
use Modules\Billing\Repository\PaymentProviderRepository;

class GetAllPaymentProvidersAction
{
    public function __construct(
        private readonly PaymentProviderRepository $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
