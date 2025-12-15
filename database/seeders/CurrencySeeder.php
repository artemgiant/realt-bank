<?php

namespace Database\Seeders;

use App\Models\Reference\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'Долар США',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Євро',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'UAH',
                'symbol' => '₴',
                'name' => 'Гривня',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
