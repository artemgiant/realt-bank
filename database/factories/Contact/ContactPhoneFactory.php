<?php

namespace Database\Factories\Contact;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания телефонов контактов
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact\ContactPhone>
 */
class ContactPhoneFactory extends Factory
{
    protected $model = ContactPhone::class;

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'phone' => $this->generatePhone(),
            'is_primary' => false,
        ];
    }

    /**
     * Генерация телефона в формате +380
     */
    private function generatePhone(): string
    {
        $operators = ['50', '63', '66', '67', '68', '73', '93', '95', '96', '97', '98', '99'];
        $operator = $this->faker->randomElement($operators);
        return '+380' . $operator . $this->faker->numerify('#######');
    }

    /**
     * Основной телефон
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
