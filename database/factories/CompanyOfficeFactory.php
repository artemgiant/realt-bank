<?php

namespace Database\Factories;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Zone;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Database\Factories\Contact\ContactFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\CompanyOffice>
 */
class CompanyOfficeFactory extends Factory
{
    protected $model = CompanyOffice::class;

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
        // Получаем случайный город из Одесской области
        $city = City::where('state_id', self::ODESSA_STATE_ID)
            ->inRandomOrder()
            ->first();

        // Получаем район и зону из города
        $district = $city ? District::where('city_id', $city->id)->inRandomOrder()->first() : null;
        $zone = $district ? Zone::where('district_id', $district->id)->inRandomOrder()->first() : null;

        $officeName = fake()->randomElement(['Главный офис', 'Офис продаж', 'Филиал', 'Представительство']);
        if ($city) {
            $officeName .= ' ' . $city->name;
        }

        return [
            'company_id' => Company::factory(),
            'name' => $officeName,
            'country_id' => self::UKRAINE_COUNTRY_ID,
            'state_id' => self::ODESSA_STATE_ID,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'street_id' => null,
            'building_number' => fake()->buildingNumber(),
            'office_number' => fake()->optional()->randomNumber(3),
            'full_address' => fake()->address(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the office is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set as main office.
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Главный офис',
            'sort_order' => 0,
        ]);
    }

    /**
     * Офис с контактами
     */
    public function withContacts(int $count = 2, bool $withPhones = true): static
    {
        return $this->afterCreating(function (CompanyOffice $office) use ($count, $withPhones) {
            $roles = ['manager', 'administrator', 'consultant', 'receptionist'];

            for ($i = 0; $i < $count; $i++) {
                $contactFactory = ContactFactory::new();

                if ($withPhones) {
                    $contactFactory = $contactFactory->withPhones(rand(1, 2));
                }

                $contact = $contactFactory->create();

                $office->contacts()->attach($contact->id, [
                    'role' => $i === 0 ? 'primary' : $roles[array_rand($roles)],
                ]);
            }
        });
    }

    /**
     * Удалить все офисы
     */
    public static function cleanAll(): int
    {
        return CompanyOffice::query()->forceDelete();
    }
}
