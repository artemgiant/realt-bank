<?php

namespace Database\Factories\Property;

use App\Models\Property\Property;
use App\Models\Property\PropertyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания документов объектов недвижимости
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property\PropertyDocument>
 */
class PropertyDocumentFactory extends Factory
{
    protected $model = PropertyDocument::class;

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $propertyId = $this->faker->numberBetween(1, 1000);
        $extension = $this->faker->randomElement(['pdf', 'doc', 'docx', 'jpg', 'png']);
        $filename = $this->faker->uuid() . '.' . $extension;

        // Названия документов на русском
        $documentNames = [
            'Договор купли-продажи',
            'Технический паспорт',
            'Выписка из реестра',
            'План квартиры',
            'Акт приема-передачи',
            'Свидетельство о праве собственности',
            'Справка БТИ',
            'Кадастровый паспорт',
            'Согласие супруга',
            'Доверенность',
            'Оценка недвижимости',
            'Фото документов',
        ];

        return [
            'property_id' => Property::factory(),
            'name' => $this->faker->randomElement($documentNames),
            'path' => "properties/{$propertyId}/documents/{$filename}",
            'filename' => $filename,
        ];
    }

    /**
     * PDF документ
     */
    public function pdf(): static
    {
        $filename = $this->faker->uuid() . '.pdf';

        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => str_replace(
                basename($attributes['path']),
                $filename,
                $attributes['path']
            ),
        ]);
    }

    /**
     * Документ Word
     */
    public function word(): static
    {
        $filename = $this->faker->uuid() . '.docx';

        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => str_replace(
                basename($attributes['path']),
                $filename,
                $attributes['path']
            ),
        ]);
    }

    /**
     * Изображение документа
     */
    public function image(): static
    {
        $extension = $this->faker->randomElement(['jpg', 'png']);
        $filename = $this->faker->uuid() . '.' . $extension;

        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => str_replace(
                basename($attributes['path']),
                $filename,
                $attributes['path']
            ),
        ]);
    }

    /**
     * Технический паспорт
     */
    public function technicalPassport(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Технический паспорт',
        ])->pdf();
    }

    /**
     * Договор
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Договор купли-продажи',
        ])->pdf();
    }
}
