<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateShippingSettingsAction;
use Modules\Settings\Models\Setting;

class ShippingSettingsController extends Controller
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateShippingSettingsAction $updateShippingSettingsAction
    ) {
        // Authorization is handled explicitly in each method
    }

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Setting::class);
        $settings = $this->findSettingAction->execute();

        return view('settings::shipping.index', [
            'settings' => $settings,
            'shippingSettings' => $settings?->shipping_settings ?? [],
        ]);
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $this->authorize('update', $setting);

        $validated = $request->validate([
            'default_shipping_method' => 'nullable|string|max:255',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'flat_rate_shipping' => 'nullable|numeric|min:0',
            'correios_enabled' => 'nullable|boolean',
            'correios_contract_enabled' => 'nullable|boolean',
            'correios_origin_cep' => 'nullable|string|max:9',
            'correios_access_token' => 'nullable|string|max:2000',
            'correios_contract_number' => 'nullable|string|max:50',
            'correios_regional_code' => 'nullable|string|max:20',
            'correios_service_codes' => 'nullable|string|max:255',
            'correios_preco_base_url' => 'nullable|url|max:255',
            'correios_prazo_base_url' => 'nullable|url|max:255',
            'correios_default_weight_grams' => 'nullable|integer|min:1',
            'correios_default_length_cm' => 'nullable|integer|min:1',
            'correios_default_width_cm' => 'nullable|integer|min:1',
            'correios_default_height_cm' => 'nullable|integer|min:1',
            'shipping_zones' => 'nullable|array',
            'shipping_zones.*.name' => 'required|string|max:255',
            'shipping_zones.*.price' => 'required|numeric|min:0',
            'estimated_delivery_days' => 'nullable|integer|min:1',
        ]);

        $validated['correios_enabled'] = $request->boolean('correios_enabled');
        $validated['correios_contract_enabled'] = $request->boolean('correios_contract_enabled');

        $this->updateShippingSettingsAction->execute($setting, $validated);

        return redirect()->route('settings.shipping.index')->with('success', 'Configuracoes de frete atualizadas com sucesso.');
    }
}
