<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Models\Setting;

class ThemeSettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::first();

        $themeSettings = $settings?->theme_settings ?? [
            'primary_color' => '#4e73df',
            'secondary_color' => '#858796',
            'sidebar_bg' => '#4e73df',
            'sidebar_text' => '#ffffff',
            'accent_color' => '#F7941D',
            'header_bg' => '#ffffff',
            'footer_bg' => '#2d2d2d',
            'body_font' => 'Poppins, sans-serif',
        ];

        return view('settings::appearance', compact('themeSettings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|size:7|starts_with:#',
            'secondary_color' => 'required|string|size:7|starts_with:#',
            'sidebar_bg' => 'required|string|size:7|starts_with:#',
            'sidebar_text' => 'required|string|size:7|starts_with:#',
            'accent_color' => 'required|string|size:7|starts_with:#',
            'header_bg' => 'required|string|size:7|starts_with:#',
            'footer_bg' => 'required|string|size:7|starts_with:#',
            'body_font' => 'required|string|max:100',
        ]);

        $setting = Setting::first();
        if ($setting) {
            $setting->update(['theme_settings' => $validated]);
            \Illuminate\Support\Facades\Cache::forget('app.settings');
        }

        return redirect()->back()->with('success', 'Tema atualizado com sucesso!');
    }
}
