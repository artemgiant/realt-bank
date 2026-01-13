<?php

namespace Database\Factories\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания переводов объектов недвижимости
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property\PropertyTranslation>
 */
class PropertyTranslationFactory extends Factory
{
    protected $model = PropertyTranslation::class;

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $locale = $this->faker->randomElement(['ru', 'ua', 'en']);

        return [
            'property_id' => Property::factory(),
            'locale' => $locale,
            'title' => $this->generateTitle($locale),
            'description' => $this->generateDescription($locale),
        ];
    }

    /**
     * Генерация заголовка на нужном языке
     */
    private function generateTitle(string $locale): string
    {
        $rooms = $this->faker->numberBetween(1, 5);
        $area = $this->faker->numberBetween(30, 150);

        $titles = [
            'ru' => [
                "{$rooms}-комнатная квартира {$area} м²",
                "Продажа {$rooms}к квартиры {$area} м²",
                "Квартира {$rooms} комнаты в новостройке",
                "Уютная {$rooms}-комнатная квартира",
                "Просторная квартира {$area} м² с ремонтом",
            ],
            'ua' => [
                "{$rooms}-кімнатна квартира {$area} м²",
                "Продаж {$rooms}к квартири {$area} м²",
                "Квартира {$rooms} кімнати в новобудові",
                "Затишна {$rooms}-кімнатна квартира",
                "Простора квартира {$area} м² з ремонтом",
            ],
            'en' => [
                "{$rooms}-room apartment {$area} sqm",
                "{$rooms} bedroom flat for sale",
                "Cozy {$rooms}-room apartment",
                "Spacious {$area} sqm apartment with renovation",
                "Modern {$rooms} bedroom apartment",
            ],
        ];

        return $this->faker->randomElement($titles[$locale] ?? $titles['ru']);
    }

    /**
     * Генерация описания на нужном языке
     */
    /**
     * Генерация описания на нужном языке
     */
    private function generateDescription(string $locale): string
    {
        $fakerLocale = match ($locale) {
            'ru' => 'ru_RU',
            'ua' => 'uk_UA',
            default => 'en_US',
        };

        $faker = \Faker\Factory::create($fakerLocale);

        return $faker->realText($this->faker->numberBetween(300, 1000));
    }

    /**
     * Перевод на русском
     */
    public function russian(): static
    {
        return $this->state(fn(array $attributes) => [
            'locale' => 'ru',
            'title' => $this->generateTitle('ru'),
            'description' => $this->generateDescription('ru'),
        ]);
    }

    /**
     * Перевод на украинском
     */
    public function ukrainian(): static
    {
        return $this->state(fn(array $attributes) => [
            'locale' => 'ua',
            'title' => $this->generateTitle('ua'),
            'description' => $this->generateDescription('ua'),
        ]);
    }

    /**
     * Перевод на английском
     */
    public function english(): static
    {
        return $this->state(fn(array $attributes) => [
            'locale' => 'en',
            'title' => $this->generateTitle('en'),
            'description' => $this->generateDescription('en'),
        ]);
    }
}
