<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\TaxClassification;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = SiteSetting::defaults();

        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();
        if ($standard !== null) {
            $defaults['default_tax_classification_id'] = $standard->id;
        }

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            $defaults
        );

        SiteSetting::clearCache();
    }
}
