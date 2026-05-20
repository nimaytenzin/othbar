<?php

namespace Database\Seeders;

use App\Models\TaxClassification;
use Illuminate\Database\Seeder;

class TaxClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $classifications = [
            [
                'code' => 'STANDARD',
                'name' => 'Standard-Rated (5%)',
                'description' => 'Most goods and services - GST charged at 5%, input credits claimable',
                'rate_percent' => 5,
                'input_credits_claimable' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'ZERO_RATED',
                'name' => 'Zero-Rated (0%)',
                'description' => 'Special supplies (e.g., exports) - 0% GST charged, input credits claimable',
                'rate_percent' => 0,
                'input_credits_claimable' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'EXEMPT',
                'name' => 'Exempt',
                'description' => 'Specific supplies (e.g., financial services, healthcare) - no GST charged, no input credits',
                'rate_percent' => 0,
                'input_credits_claimable' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($classifications as $data) {
            TaxClassification::query()->updateOrCreate(
                ['code' => $data['code']],
                $data,
            );
        }
    }
}
