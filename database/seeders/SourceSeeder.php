<?php

namespace Database\Seeders;

use App\Models\Reference\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Рекомендація',
            'OLX',
            'DOM.RIA',
            'Знайомі',
            'Власний пошук',
            'Телефонний дзвінок',
            'Сайт компанії',
            'Instagram',
            'Facebook',
            'Telegram',
            'Повторний клієнт',
            'Інше',
        ];

        foreach ($sources as $name) {
            Source::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true,
                ]
            );
        }
    }
}
