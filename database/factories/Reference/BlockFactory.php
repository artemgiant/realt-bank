<?php

namespace Database\Factories\Reference;

use App\Models\Location\Street;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Dictionary;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Фабрика для создания блоков комплексов
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\Block>
 */
class BlockFactory extends Factory
{
    protected $model = Block::class;

    /**
     * Названия блоков/секций
     */
    private array $blockNames = [
        'Секция А', 'Секция Б', 'Секция В', 'Секция Г', 'Секция Д',
        'Корпус 1', 'Корпус 2', 'Корпус 3', 'Корпус 4', 'Корпус 5',
        'Блок А', 'Блок Б', 'Блок В',
        'Литер А', 'Литер Б', 'Литер В',
        'Башня 1', 'Башня 2', 'Башня 3',
    ];

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement($this->blockNames);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(5),
            'building_number' => $this->faker->optional(0.7)->numerify('##') . $this->faker->optional(0.3)->randomElement(['А', 'Б', 'В', '']),
            'floors_total' => $this->faker->numberBetween(5, 25),
            'year_built' => $this->faker->optional(0.6)->numberBetween(2020, 2027),
            'heating_type_id' => Dictionary::where('type', Dictionary::TYPE_HEATING_TYPE)->inRandomOrder()->value('id'),
            'wall_type_id' => Dictionary::where('type', Dictionary::TYPE_WALL_TYPE)->inRandomOrder()->value('id'),
            'plan_path' => null,
            'is_active' => true,
            'source' => 'import',
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Block $block) {
            // Привязываем улицу из зоны комплекса, если есть
            if ($block->complex && $block->complex->zone_id) {
                $street = Street::where('zone_id', $block->complex->zone_id)
                    ->inRandomOrder()
                    ->first();

                if ($street) {
                    $block->update(['street_id' => $street->id]);
                }
            }
        });
    }

    /**
     * Для конкретного комплекса
     */
    public function forComplex(Complex $complex): static
    {
        return $this->state(fn(array $attributes) => [
            'complex_id' => $complex->id,
            'developer_id' => $complex->developer_id,
        ]);
    }

    /**
     * Неактивный блок
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Блок со сданным годом постройки
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'year_built' => $this->faker->numberBetween(2018, 2024),
        ]);
    }

    /**
     * Блок в процессе строительства
     */
    public function underConstruction(): static
    {
        return $this->state(fn(array $attributes) => [
            'year_built' => $this->faker->numberBetween(2025, 2028),
        ]);
    }

    /**
     * Удалить все блоки, созданные через импорт/фабрику
     */
    public static function cleanImported(): int
    {
        return Block::where('source', 'import')->forceDelete();
    }
}
