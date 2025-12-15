<?php

namespace Database\Seeders;

use App\Models\Location\Country;
use App\Models\Location\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========== Україна ==========
        $ukraine = Country::updateOrCreate(
            ['code' => 'UA'],
            [
                'name' => 'Україна',
                'code' => 'UA',
                'is_active' => true,
            ]
        );

        // Області України
        $regions = [
            'Вінницька область',
            'Волинська область',
            'Дніпропетровська область',
            'Донецька область',
            'Житомирська область',
            'Закарпатська область',
            'Запорізька область',
            'Івано-Франківська область',
            'Київська область',
            'Кіровоградська область',
            'Луганська область',
            'Львівська область',
            'Миколаївська область',
            'Одеська область',
            'Полтавська область',
            'Рівненська область',
            'Сумська область',
            'Тернопільська область',
            'Харківська область',
            'Херсонська область',
            'Хмельницька область',
            'Черкаська область',
            'Чернівецька область',
            'Чернігівська область',
            'Київ',
            'Севастополь',
            'АР Крим',
        ];

        foreach ($regions as $regionName) {
            Region::updateOrCreate(
                [
                    'country_id' => $ukraine->id,
                    'slug' => Str::slug($regionName),
                ],
                [
                    'country_id' => $ukraine->id,
                    'name' => $regionName,
                    'slug' => Str::slug($regionName),
                    'is_active' => true,
                ]
            );
        }

        // ========== Інші країни (опційно) ==========
        $otherCountries = [
            ['name' => 'Польща', 'code' => 'PL'],
            ['name' => 'Німеччина', 'code' => 'DE'],
            ['name' => 'Іспанія', 'code' => 'ES'],
            ['name' => 'Туреччина', 'code' => 'TR'],
            ['name' => 'ОАЕ', 'code' => 'AE'],
            ['name' => 'Кіпр', 'code' => 'CY'],
            ['name' => 'Чорногорія', 'code' => 'ME'],
            ['name' => 'Болгарія', 'code' => 'BG'],
        ];

        foreach ($otherCountries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'code' => $country['code'],
                    'is_active' => true,
                ]
            );
        }
    }
}
