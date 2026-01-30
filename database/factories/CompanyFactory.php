<?php

namespace Database\Factories;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Zone;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Database\Factories\Contact\ContactFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\Company>
 *
 * # Удалить все и создать 20 компаний с офисами и контактами
 * php artisan tinker --execute="use Database\Factories\CompanyFactory; CompanyFactory::cleanAll(); App\Models\Reference\Company::factory()->count(20)->withContacts(2)->withOffices(rand(1,3), 2)->create();"
 *
 * # Создать компании без контактов у офисов
 * php artisan tinker --execute="App\Models\Reference\Company::factory()->count(5)->withContacts(1)->withOffices(2, 0)->create();"
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
     * Компания с контактами
     */
    public function withContacts(int $count = 2, bool $withPhones = true): static
    {
        return $this->afterCreating(function (Company $company) use ($count, $withPhones) {
            $roles = ['director', 'manager', 'accountant', 'secretary'];

            for ($i = 0; $i < $count; $i++) {
                $contactFactory = ContactFactory::new();

                if ($withPhones) {
                    $contactFactory = $contactFactory->withPhones(rand(1, 2));
                }

                $contact = $contactFactory->create();

                $company->contacts()->attach($contact->id, [
                    'role' => $i === 0 ? 'primary' : $roles[array_rand($roles)],
                ]);
            }
        });
    }

    /**
     * Компания с офисами (и опционально с контактами)
     */
    public function withOffices(int $count = 2, int $contactsPerOffice = 1, bool $withPhones = true): static
    {
        return $this->afterCreating(function (Company $company) use ($count, $contactsPerOffice, $withPhones) {
            for ($i = 0; $i < $count; $i++) {
                $officeFactory = CompanyOfficeFactory::new();

                if ($contactsPerOffice > 0) {
                    $officeFactory = $officeFactory->withContacts($contactsPerOffice, $withPhones);
                }

                $officeFactory->create(['company_id' => $company->id]);
            }
        });
    }

    /**
     * Удалить все компании, офисы и связанные контакты
     */
    public static function cleanAll(): array
    {
        // Получаем ID контактов, связанных с компаниями
        $companyContactIds = \DB::table('contactables')
            ->where('contactable_type', Company::class)
            ->pluck('contact_id')
            ->toArray();

        // Получаем ID контактов, связанных с офисами
        $officeContactIds = \DB::table('contactables')
            ->where('contactable_type', CompanyOffice::class)
            ->pluck('contact_id')
            ->toArray();

        $allContactIds = array_unique(array_merge($companyContactIds, $officeContactIds));

        // Удаляем связи в pivot таблице
        $deletedContactables = \DB::table('contactables')
            ->whereIn('contactable_type', [Company::class, CompanyOffice::class])
            ->delete();

        // Удаляем телефоны контактов
        $deletedPhones = ContactPhone::whereIn('contact_id', $allContactIds)->forceDelete();

        // Удаляем контакты
        $deletedContacts = Contact::whereIn('id', $allContactIds)->forceDelete();

        // Удаляем офисы и компании
        $deletedOffices = CompanyOffice::query()->forceDelete();
        $deletedCompanies = Company::query()->forceDelete();

        return [
            'companies' => $deletedCompanies,
            'offices' => $deletedOffices,
            'contacts' => $deletedContacts,
            'phones' => $deletedPhones,
            'contactables' => $deletedContactables,
        ];
    }
}
