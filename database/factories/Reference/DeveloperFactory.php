<?php

namespace Database\Factories\Reference;

use App\Models\Location\City;
use App\Models\Location\State;
use App\Models\Reference\Developer;
use App\Models\Reference\DeveloperLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\Developer>
 */
class DeveloperFactory extends Factory
{
    protected $model = Developer::class;

    /**
     * ID Одесской Регионы
     */
    protected const ODESSA_STATE_ID = 14;

    /**
     * Удалить всех девелоперов где source = 'manual' перед созданием новых
     */
    public static function cleanManual(): int
    {
        return Developer::where('source', 'manual')->forceDelete();
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(5),
            'website' => fake()->optional()->url(),
            'description' => fake()->optional()->paragraph(),
            'is_active' => true,
            'source' => 'manual',
            'year_founded' => fake()->optional()->numberBetween(1990, 2024),
            'agent_notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Developer $developer) {
            $this->attachRandomOdessaCity($developer);

            // Создаем контакт представителя девелопера
            $contact = \Database\Factories\Contact\ContactFactory::new()
                ->developerRepresentative()
                ->withPrimaryPhone()
                ->create();

            // Привязываем контакт к девелоперу
            $developer->contacts()->attach($contact->id, ['role' => 'primary']);
        });
    }

    /**
     * Привязать случайный город из Одесской Регионы к девелоперу
     */
    protected function attachRandomOdessaCity(Developer $developer): void
    {
        $city = City::where('state_id', self::ODESSA_STATE_ID)
            ->inRandomOrder()
            ->first();

        if ($city) {
            $state = State::find(self::ODESSA_STATE_ID);
            $fullLocationName = $state ? "{$state->name}, {$city->name}" : $city->name;

            DeveloperLocation::create([
                'developer_id' => $developer->id,
                'location_type' => 'city',
                'location_id' => $city->id,
                'location_name' => $city->name,
                'full_location_name' => $fullLocationName,
            ]);
        }
    }

    /**
     * Удалить девелоперов созданных вручную и создать новых
     */
    public function cleanAndCreate(int $count = 1): \Illuminate\Database\Eloquent\Collection
    {
        self::cleanManual();
        return $this->count($count)->create();
    }

    /**
     * Indicate that the developer is imported.
     */
    public function imported(): static
    {
        return $this->state(fn(array $attributes) => [
            'source' => 'import',
        ]);
    }

    /**
     * Indicate that the developer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
