<?php

namespace Database\Factories\Contact;

use App\Models\Contact\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания контактов
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Имена на русском
     */
    private array $firstNames = [
        'male' => ['Александр', 'Дмитрий', 'Сергей', 'Андрей', 'Максим', 'Иван', 'Михаил', 'Николай', 'Владимир', 'Олег', 'Виктор', 'Павел', 'Артем', 'Роман', 'Евгений'],
        'female' => ['Анна', 'Мария', 'Елена', 'Ольга', 'Наталья', 'Ирина', 'Светлана', 'Татьяна', 'Юлия', 'Екатерина', 'Виктория', 'Алина', 'Дарья', 'Полина', 'Ксения'],
    ];

    /**
     * Фамилии на русском
     */
    private array $lastNames = [
        'male' => ['Иванов', 'Петров', 'Сидоров', 'Козлов', 'Новиков', 'Морозов', 'Волков', 'Соколов', 'Лебедев', 'Кузнецов', 'Попов', 'Васильев', 'Павлов', 'Семенов', 'Голубев'],
        'female' => ['Иванова', 'Петрова', 'Сидорова', 'Козлова', 'Новикова', 'Морозова', 'Волкова', 'Соколова', 'Лебедева', 'Кузнецова', 'Попова', 'Васильева', 'Павлова', 'Семенова', 'Голубева'],
    ];

    /**
     * Отчества на русском
     */
    private array $middleNames = [
        'male' => ['Александрович', 'Дмитриевич', 'Сергеевич', 'Андреевич', 'Максимович', 'Иванович', 'Михайлович', 'Николаевич', 'Владимирович', 'Олегович', 'Викторович', 'Павлович'],
        'female' => ['Александровна', 'Дмитриевна', 'Сергеевна', 'Андреевна', 'Максимовна', 'Ивановна', 'Михайловна', 'Николаевна', 'Владимировна', 'Олеговна', 'Викторовна', 'Павловна'],
    ];

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'first_name' => $this->faker->randomElement($this->firstNames[$gender]),
            'last_name' => $this->faker->randomElement($this->lastNames[$gender]),
            'middle_name' => $this->faker->boolean(70) ? $this->faker->randomElement($this->middleNames[$gender]) : null,
            'email' => $this->faker->boolean(60) ? $this->faker->unique()->safeEmail() : null,
            'contact_type' => $this->faker->randomElement([
                Contact::TYPE_OWNER,
                Contact::TYPE_AGENT,
                Contact::TYPE_DEVELOPER,
                Contact::TYPE_DEVELOPER_REPRESENTATIVE
            ]),
            'tags' => $this->faker->boolean(30) ? $this->faker->randomElement(['VIP', 'Срочно', 'Постоянный клиент', 'Новый']) : null,
            'telegram' => $this->faker->boolean(40) ? 'https://t.me/' . $this->faker->userName() : null,
            'viber' => $this->faker->boolean(30) ? 'viber://chat?number=' . $this->generatePhone() : null,
            'whatsapp' => $this->faker->boolean(40) ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $this->generatePhone()) : null,
            'passport' => $this->faker->boolean(20) ? strtoupper($this->faker->randomLetter() . $this->faker->randomLetter()) . ' ' . $this->faker->numerify('######') : null,
            'inn' => $this->faker->boolean(20) ? $this->faker->numerify('##########') : null,
            'photo' => null,
            'comment' => $this->faker->boolean(30) ? $this->faker->sentence(5) : null,
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
     * Контакт типа "Владелец"
     */
    public function owner(): static
    {
        return $this->state(fn(array $attributes) => [
            'contact_type' => Contact::TYPE_OWNER,
        ]);
    }

    /**
     * Контакт типа "Агент"
     */
    public function agent(): static
    {
        return $this->state(fn(array $attributes) => [
            'contact_type' => Contact::TYPE_AGENT,
        ]);
    }

    /**
     * Контакт типа "Девелопер"
     */
    public function developer(): static
    {
        return $this->state(fn(array $attributes) => [
            'contact_type' => Contact::TYPE_DEVELOPER,
        ]);
    }

    /**
     * Контакт типа "Представитель девелопера"
     */
    public function developerRepresentative(): static
    {
        return $this->state(fn(array $attributes) => [
            'contact_type' => Contact::TYPE_DEVELOPER_REPRESENTATIVE,
        ]);
    }

    /**
     * Контакт с телефонами
     */
    public function withPhones(int $count = 2): static
    {
        return $this->afterCreating(function (Contact $contact) use ($count) {
            ContactPhoneFactory::new()
                ->count($count)
                ->sequence(fn($sequence) => [
                    'is_primary' => $sequence->index === 0,
                ])
                ->create(['contact_id' => $contact->id]);
        });
    }

    /**
     * Контакт с одним основным телефоном
     */
    public function withPrimaryPhone(): static
    {
        return $this->afterCreating(function (Contact $contact) {
            ContactPhoneFactory::new()
                ->primary()
                ->create(['contact_id' => $contact->id]);
        });
    }
}
