<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->loadGlobalHelpers();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model::shouldBeStrict(); // Disabled for translations to work
        Model::automaticallyEagerLoadRelationships();

        // Set default pagination view for admin panel
        // This ensures all pagination in admin panel uses the custom view
        LengthAwarePaginator::defaultView('pagination::admin-bootstrap-5');
    }

    private function loadGlobalHelpers(): void
    {
        $helpers = [
            base_path('Modules/Front/Helpers/theme.php'),
            base_path('app/Helpers/LocaleHelper.php'),
            base_path('Modules/GeoLocalization/Helpers/GeoLocalizationHelper.php'),
        ];

        foreach ($helpers as $helper) {
            if (is_file($helper)) {
                require_once $helper;
            }
        }
    }
}
