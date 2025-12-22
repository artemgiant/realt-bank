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
    private function generateDescription(string $locale): string
    {
        $descriptions = [
            'ru' => [
                'Отличная квартира в тихом районе. Развитая инфраструктура, рядом школы, детские сады, магазины. Хорошее транспортное сообщение.',
                'Светлая просторная квартира с качественным ремонтом. Встроенная кухня, кондиционер, бронированная дверь. Чистый подъезд.',
                'Квартира после капитального ремонта. Новая сантехника, электропроводка. Тихий двор, парковка. Документы готовы.',
                'Уютная квартира в кирпичном доме. Высокие потолки, большие окна. Закрытая территория, консьерж.',
                'Продается квартира в отличном состоянии. Два балкона, раздельный санузел. Автономное отопление.',
            ],
            'ua' => [
                'Чудова квартира в тихому районі. Розвинена інфраструктура, поряд школи, дитячі садки, магазини. Гарне транспортне сполучення.',
                'Світла простора квартира з якісним ремонтом. Вбудована кухня, кондиціонер, броньовані двері. Чистий під\'їзд.',
                'Квартира після капітального ремонту. Нова сантехніка, електропроводка. Тихий двір, паркування. Документи готові.',
                'Затишна квартира в цегляному будинку. Високі стелі, великі вікна. Закрита територія, консьєрж.',
                'Продається квартира у відмінному стані. Два балкони, роздільний санвузол. Автономне опалення.',
            ],
            'en' => [
                'Excellent apartment in a quiet area. Developed infrastructure, nearby schools, kindergartens, shops. Good transport links.',
                'Bright spacious apartment with quality renovation. Built-in kitchen, air conditioning, armored door. Clean entrance.',
                'Apartment after major renovation. New plumbing, electrical wiring. Quiet courtyard, parking. Documents ready.',
                'Cozy apartment in a brick building. High ceilings, large windows. Gated community, concierge.',
                'Apartment for sale in excellent condition. Two balconies, separate bathroom. Autonomous heating.',
            ],
        ];

        return $this->faker->randomElement($descriptions[$locale] ?? $descriptions['ru']);
    }

    /**
     * Перевод на русском
     */
    public function russian(): static
    {
        return $this->state(fn (array $attributes) => [
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
        return $this->state(fn (array $attributes) => [
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
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
            'title' => $this->generateTitle('en'),
            'description' => $this->generateDescription('en'),
        ]);
    }
}
