<?php

declare(strict_types=1);

namespace Modules\Language\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Language\Models\Language;

class LanguageDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Language::where('code', '!=', 'pt')->update(['is_default' => false]);

        $languages = [
            [
                'code' => 'pt',
                'name' => 'Português do Brasil',
                'native_name' => 'Português do Brasil',
                'flag' => 'BR',
                'is_default' => true,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 1,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => 'GB',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 2,
            ],
            [
                'code' => 'mk',
                'name' => 'Macedonian',
                'native_name' => 'Macedonian',
                'flag' => 'MK',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 3,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'flag' => 'DE',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 4,
            ],
            [
                'code' => 'sq',
                'name' => 'Albanian',
                'native_name' => 'Shqip',
                'flag' => 'AL',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 5,
            ],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
