<?php

namespace Database\Factories\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания фото объектов недвижимости
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property\PropertyPhoto>
 */
class PropertyPhotoFactory extends Factory
{
    protected $model = PropertyPhoto::class;

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $propertyId = $this->faker->numberBetween(1, 1000);
        $filename = $this->faker->uuid() . '.jpg';

        return [
            'property_id' => Property::factory(),
            'path' => "properties/{$propertyId}/photos/{$filename}",
            'filename' => $filename,
            'sort_order' => $this->faker->numberBetween(1, 10),
            'is_main' => false,
        ];
    }

    /**
     * Фото как главное
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * Фото с указанным порядком сортировки
     */
    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }

    /**
     * Фото с placeholder URL (для тестов без реальных файлов)
     */
    public function placeholder(): static
    {
        $width = $this->faker->randomElement([800, 1024, 1280]);
        $height = $this->faker->randomElement([600, 768, 720]);

        return $this->state(fn (array $attributes) => [
            'path' => "https://placehold.co/{$width}x{$height}/png",
            'filename' => "placeholder_{$width}x{$height}.png",
        ]);
    }
}
