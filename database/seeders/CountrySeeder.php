<?php

namespace Database\Seeders;

use App\Models\Location\Country;
use App\Models\Location\Region;
use App\Models\Location\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========== Украина ==========
        $ukraine = Country::updateOrCreate(
            ['code' => 'UA'],
            [
                'name' => 'Украина',
                'code' => 'UA',
                'is_active' => true,
            ]
        );

        // Области Украины
        $regions = [
            'Винницкая область',
            'Волынская область',
            'Днепропетровская область',
            'Донецкая область',
            'Житомирская область',
            'Закарпатская область',
            'Запорожская область',
            'Ивано-Франковская область',
            'Киевская область',
            'Кировоградская область',
            'Луганская область',
            'Львовская область',
            'Николаевская область',
            'Одесская область',
            'Полтавская область',
            'Ровенская область',
            'Сумская область',
            'Тернопольская область',
            'Харьковская область',
            'Херсонская область',
            'Хмельницкая область',
            'Черкасская область',
            'Черновицкая область',
            'Черниговская область',
            'Киев',
            'Севастополь',
            'АР Крым',
        ];

        foreach ($regions as $regionName) {
            State::updateOrCreate(
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

        // ========== Другие страны ==========
        $otherCountries = [
            ['name' => 'Польша', 'code' => 'PL'],
            ['name' => 'Германия', 'code' => 'DE'],
            ['name' => 'Испания', 'code' => 'ES'],
            ['name' => 'Турция', 'code' => 'TR'],
            ['name' => 'ОАЭ', 'code' => 'AE'],
            ['name' => 'Кипр', 'code' => 'CY'],
            ['name' => 'Черногория', 'code' => 'ME'],
            ['name' => 'Болгария', 'code' => 'BG'],
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
