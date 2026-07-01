<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Billing\Actions\PaymentProvider\CreatePaymentProviderAction;
use Modules\Billing\Actions\PaymentProvider\DeletePaymentProviderAction;
use Modules\Billing\Actions\PaymentProvider\FindPaymentProviderAction;
use Modules\Billing\Actions\PaymentProvider\GetAllPaymentProvidersAction;
use Modules\Billing\Actions\PaymentProvider\UpdatePaymentProviderAction;
use Modules\Billing\DTOs\PaymentProviderDTO;
use Modules\Billing\Http\Requests\PaymentProvider\Store;
use Modules\Billing\Http\Requests\PaymentProvider\Update;
use Modules\Billing\Models\PaymentProvider;
use Modules\Core\Http\Controllers\CoreController;

class PaymentProviderController extends CoreController
{
    public function __construct(
        private readonly GetAllPaymentProvidersAction $getAllAction,
        private readonly FindPaymentProviderAction $findAction,
        private readonly CreatePaymentProviderAction $createAction,
        private readonly UpdatePaymentProviderAction $updateAction,
        private readonly DeletePaymentProviderAction $deleteAction,
    ) {
        $this->authorizeResource(PaymentProvider::class, 'payment_provider');
    }

    public function index(): View
    {
        return view('billing::index', [
            'paymentProviders' => $this->getAllAction->execute(),
        ]);
    }

    public function create(): View
    {
        return view('billing::create', [
            'paymentProvider' => new PaymentProvider,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = PaymentProviderDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('payment_provider.index')
            ->with('success', __('Payment provider created successfully.'));
    }

    public function edit(PaymentProvider $payment_provider): View
    {
        return view('billing::edit', [
            'paymentProvider' => $this->findAction->execute($payment_provider->id),
        ]);
    }

    public function update(Update $request, PaymentProvider $payment_provider): RedirectResponse
    {
        $dto = PaymentProviderDTO::fromRequest($request, $payment_provider->id, $payment_provider);
        $this->updateAction->execute($dto);

        return redirect()->route('payment_provider.index')
            ->with('success', __('Payment provider updated successfully.'));
    }

    public function destroy(PaymentProvider $payment_provider): RedirectResponse
    {
        $this->deleteAction->execute($payment_provider->id);

        return redirect()->route('payment_provider.index')
            ->with('success', __('Payment provider deleted successfully.'));
    }
}
