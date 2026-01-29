<?php

namespace Database\Factories;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Zone;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\Company>
 *
 * # Удалить все и создать 20 компаний с 1-5 офисами
php artisan tinker --execute="use Database\Factories\CompanyFactory; CompanyFactory::cleanAll(); App\Models\Reference\Company::factory()->count(20)->create()->each(fn(\$c) => App\Models\Reference\CompanyOffice::factory()->count(rand(1,5))->create(['company_id' => \$c->id]));"

 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * ID Одесской области
     */
    protected const ODESSA_STATE_ID = 14;

    /**
     * ID Украины
     */
    protected const UKRAINE_COUNTRY_ID = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        // Получаем случайный город из Одесской области
        $city = City::where('state_id', self::ODESSA_STATE_ID)
            ->inRandomOrder()
            ->first();

        // Получаем район и зону из города
        $district = $city ? District::where('city_id', $city->id)->inRandomOrder()->first() : null;
        $zone = $district ? Zone::where('district_id', $district->id)->inRandomOrder()->first() : null;

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(5),
            'name_translations' => [
                'ru' => $name,
                'uk' => $name,
                'en' => $name,
            ],
            'description_translations' => [
                'ru' => fake()->paragraph(),
                'uk' => fake()->paragraph(),
                'en' => fake()->paragraph(),
            ],
            'country_id' => self::UKRAINE_COUNTRY_ID,
            'state_id' => self::ODESSA_STATE_ID,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'street_id' => null,
            'building_number' => fake()->buildingNumber(),
            'office_number' => fake()->optional()->randomNumber(3),
            'website' => fake()->optional()->url(),
            'edrpou_code' => fake()->unique()->numerify('########'),
            'company_type' => fake()->randomElement(['agency', 'developer', 'broker']),
            'logo_path' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set company type to agency.
     */
    public function agency(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'agency',
        ]);
    }

    /**
     * Set company type to developer.
     */
    public function developer(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'developer',
        ]);
    }

    /**
     * Set company type to broker.
     */
    public function broker(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'broker',
        ]);
    }

    /**
     * Удалить все компании и их офисы
     */
    public static function cleanAll(): array
    {
        $deletedOffices = CompanyOffice::query()->forceDelete();
        $deletedCompanies = Company::query()->forceDelete();

        return [
            'companies' => $deletedCompanies,
            'offices' => $deletedOffices,
        ];
    }
}
