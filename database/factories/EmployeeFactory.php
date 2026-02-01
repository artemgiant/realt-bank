<?php

namespace Database\Factories;

use App\Models\Employee\Employee;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\Reference\Dictionary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee\Employee>
 *
 * # Создать 100 сотрудников
 * php artisan tinker --execute="App\Models\Employee\Employee::factory()->count(100)->create();"
 *
 * # Удалить всех и создать заново
 * php artisan tinker --execute="App\Models\Employee\Employee::query()->forceDelete(); App\Models\Employee\Employee::factory()->count(100)->create();"
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first();
        $office = $company
            ? CompanyOffice::where('company_id', $company->id)->inRandomOrder()->first()
            : null;

        $position = Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_POSITION)
            ->inRandomOrder()
            ->first();

        $status = Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_STATUS)
            ->inRandomOrder()
            ->first();

        $tags = Dictionary::where('type', Dictionary::TYPE_AGENT_TAG)
            ->inRandomOrder()
            ->limit(rand(0, 5))
            ->pluck('id')
            ->toArray();

        $firstName = fake('uk_UA')->firstName();
        $lastName = fake('uk_UA')->lastName();
        $middleName = fake()->boolean(70) ? fake('uk_UA')->firstName() . 'ович' : null;

        return [
            'user_id' => null,
            'company_id' => $company?->id,
            'office_id' => $office?->id,
            'position_id' => $position?->id,
            'status_id' => $status?->id,
            'tag_ids' => $tags ?: null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+380#########'),
            'birthday' => fake()->dateTimeBetween('-60 years', '-20 years'),
            'passport' => fake()->boolean(50) ? fake()->numerify('##########') : null,
            'inn' => fake()->boolean(50) ? fake()->numerify('##########') : null,
            'comment' => fake()->boolean(30) ? fake()->sentence() : null,
            'photo_path' => null,
            'active_until' => fake()->boolean(20) ? fake()->dateTimeBetween('now', '+1 year') : null,
            'is_active' => fake()->boolean(90),
        ];
    }

    /**
     * Активный сотрудник
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'status_id' => Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_STATUS)
                ->where('slug', 'aktivnii')
                ->first()?->id,
        ]);
    }

    /**
     * Неактивный сотрудник
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status_id' => Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_STATUS)
                ->where('slug', 'neaktivnii')
                ->first()?->id,
        ]);
    }

    /**
     * Привязать к конкретной компании
     */
    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
            'office_id' => CompanyOffice::where('company_id', $company->id)
                ->inRandomOrder()
                ->first()?->id,
        ]);
    }

    /**
     * Привязать к конкретному офису
     */
    public function forOffice(CompanyOffice $office): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $office->company_id,
            'office_id' => $office->id,
        ]);
    }
}
