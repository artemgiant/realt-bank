<?php

namespace Database\Factories\Reference;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Zone;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use App\Models\Reference\Dictionary;
use Database\Factories\Contact\ContactFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Фабрика для создания комплексов
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reference\Complex>
 * # Создать 100 комплексов со всеми связями (контакты + блоки)
php artisan complexes:generate 100

# С указанием количества блоков (по 5 блоков на комплекс)
php artisan complexes:generate 100 --blocks=5

# Только премиум комплексы
php artisan complexes:generate 100 --premium

# Только эконом комплексы
php artisan complexes:generate 100 --economy

# Удалить старые и создать новые
php artisan complexes:generate 100 --clean
 */
class ComplexFactory extends Factory
{
    protected $model = Complex::class;

    /**
     * ID Одесской области
     */
    protected const ODESSA_STATE_ID = 14;

    /**
     * Названия комплексов
     */
    private array $complexNames = [
        'Новая Аркадия', 'Маринист', 'Гагарин Плаза', 'Море Плаза', 'Континент',
        'Жемчужина', 'Кадорр Сити', 'Таировские сады', 'Ривьера', 'Престиж',
        'Альтаир', 'Скай Сити', 'Парк Авеню', 'Оранж Парк', 'Солнечный',
        'Изумрудный', 'Лазурный', 'Бриз', 'Акватория', 'Панорама',
    ];

    /**
     * Описания комплексов (О комплексе)
     */
    private array $descriptions = [
        'Современный жилой комплекс премиум-класса с развитой инфраструктурой. Территория комплекса благоустроена: детские и спортивные площадки, зоны отдыха, подземный паркинг. Квартиры с панорамными окнами и качественной отделкой.',
        'Комфортабельный жилой комплекс в экологически чистом районе города. Закрытая охраняемая территория, консьерж-сервис, подземный паркинг. Квартиры с индивидуальным отоплением и умным домом.',
        'Элитный жилой комплекс с видом на море. Собственный пляж, фитнес-центр, бассейн на территории. Премиальная отделка квартир, панорамное остекление, террасы.',
        'Жилой комплекс бизнес-класса в центре города. Пешая доступность до всех объектов инфраструктуры. Охраняемая территория, детский сад и школа рядом.',
        'Уютный жилой комплекс в тихом районе. Зеленая территория, детские площадки, удобный подъезд. Квартиры с качественным ремонтом от застройщика.',
    ];

    /**
     * Примечания для агентов
     */
    private array $agentNotes = [
        'Работаем напрямую с застройщиком. Возможна рассрочка до 5 лет. Скидка при 100% оплате - 5%.',
        'Комиссия агенту 2% от суммы сделки. Бронь квартиры бесплатная на 3 дня. Показы по предварительной записи.',
        'Эксклюзивные условия для наших агентов. Расширенная комиссия при продаже более 3 объектов в месяц.',
        'Застройщик предоставляет маркетинговые материалы. Возможна помощь с ипотекой через банки-партнеры.',
        'Актуальный прайс запрашивать у менеджера. Цены могут меняться еженедельно. Горячие предложения - отдельный список.',
        'Работаем только по договору. Обязательная регистрация клиента перед показом. CRM-фиксация.',
    ];

    /**
     * Специальные условия (акции)
     */
    private array $specialConditions = [
        'Акция: При покупке квартиры - кладовая в подарок! Действует до конца месяца.',
        'Специальное предложение: Рассрочка 0% на 24 месяца. Первый взнос от 30%.',
        'Скидка 10% на последние квартиры в сданном доме. Ключи сразу после оплаты.',
        'Паркинг в подарок при покупке квартиры от 80 м2. Количество мест ограничено.',
        'Trade-in: Зачтем вашу квартиру в счет оплаты. Бесплатная оценка недвижимости.',
        'Ипотека от 0.01% годовых от банков-партнеров. Одобрение за 1 день.',
        'Бонус: дизайн-проект в подарок при 100% оплате. Стоимость подарка до $5000.',
    ];

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement($this->complexNames) . ' ' . $this->faker->randomNumber(2);

        // Получаем случайный город из Одесской области
        $city = City::where('state_id', self::ODESSA_STATE_ID)
            ->inRandomOrder()
            ->first();

        // Получаем район и зону из города
        $district = $city ? District::where('city_id', $city->id)->inRandomOrder()->first() : null;
        $zone = $district ? Zone::where('district_id', $district->id)->inRandomOrder()->first() : null;

        // Получаем или создаем застройщика
        $developer = Developer::inRandomOrder()->first()
            ?? DeveloperFactory::new()->create();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(5),
            'developer_id' => $developer->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'description' => $this->faker->randomElement($this->descriptions),
            'website' => $this->faker->optional(0.7)->url(),
            'company_website' => $this->faker->optional(0.5)->url(),
            'materials_url' => $this->faker->optional(0.4)->url(),
            'agent_notes' => $this->faker->randomElement($this->agentNotes),
            'special_conditions' => $this->faker->randomElement($this->specialConditions),
            'housing_class_id' => Dictionary::where('type', Dictionary::TYPE_HOUSING_CLASS)->inRandomOrder()->value('id'),
            'name_translations' => [
                'ua' => $name,
                'ru' => $name,
                'en' => Str::slug($name, ' '),
            ],
            'description_translations' => null,
            'logo_path' => null,
            'photos' => [],
            'plans' => [],
            'area_from' => $this->faker->randomFloat(2, 25, 50),
            'area_to' => $this->faker->randomFloat(2, 80, 200),
            'price_per_m2' => $this->faker->randomFloat(2, 800, 3000),
            'price_total' => null,
            'currency' => 'USD',
            'category_id' => null,
            'object_type_id' => null,
            'objects_count' => $this->faker->numberBetween(50, 500),
            'conditions' => $this->getRandomDictionaryIds(Dictionary::TYPE_CONDITION, 2),
            'features' => $this->getRandomDictionaryIds(Dictionary::TYPE_FEATURE, 4),
            'is_active' => true,
        ];
    }

    /**
     * Получить случайные ID из справочника
     */
    private function getRandomDictionaryIds(string $type, int $max = 3): array
    {
        return Dictionary::where('type', $type)
            ->inRandomOrder()
            ->limit($this->faker->numberBetween(1, $max))
            ->pluck('id')
            ->toArray();
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Complex $complex) {
            // Создаем контакт представителя комплекса
            $contact = ContactFactory::new()
                ->developerRepresentative()
                ->withPrimaryPhone()
                ->create();

            // Привязываем контакт к комплексу
            $complex->contacts()->attach($contact->id, ['role' => 'primary']);

            // Создаем 2-4 блока для комплекса
            BlockFactory::new()
                ->count($this->faker->numberBetween(2, 4))
                ->forComplex($complex)
                ->create();
        });
    }

    /**
     * С контактами
     */
    public function withContacts(int $count = 1): static
    {
        return $this->afterCreating(function (Complex $complex) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $contact = ContactFactory::new()
                    ->developerRepresentative()
                    ->withPrimaryPhone()
                    ->create();

                $role = $i === 0 ? 'primary' : 'secondary';
                $complex->contacts()->attach($contact->id, ['role' => $role]);
            }
        });
    }

    /**
     * С блоками
     */
    public function withBlocks(int $count = 3): static
    {
        return $this->afterCreating(function (Complex $complex) use ($count) {
            BlockFactory::new()
                ->count($count)
                ->forComplex($complex)
                ->create();
        });
    }

    /**
     * Без автоматического создания связей (контактов и блоков)
     */
    public function withoutRelations(): static
    {
        return $this->afterCreating(function (Complex $complex) {
            // Переопределяем configure() - ничего не делаем
        });
    }

    /**
     * Для конкретного застройщика
     */
    public function forDeveloper(Developer $developer): static
    {
        return $this->state(fn(array $attributes) => [
            'developer_id' => $developer->id,
        ]);
    }

    /**
     * Неактивный комплекс
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Премиум-класс
     */
    public function premium(): static
    {
        return $this->state(fn(array $attributes) => [
            'price_per_m2' => $this->faker->randomFloat(2, 2500, 5000),
            'description' => 'Элитный жилой комплекс премиум-класса с эксклюзивной инфраструктурой. Приватная территория, консьерж-сервис 24/7, спа-центр, бассейн на крыше. Квартиры с авторским дизайном и террасами.',
        ]);
    }

    /**
     * Эконом-класс
     */
    public function economy(): static
    {
        return $this->state(fn(array $attributes) => [
            'price_per_m2' => $this->faker->randomFloat(2, 500, 900),
            'description' => 'Доступный жилой комплекс эконом-класса. Удобное расположение, базовая инфраструктура района. Квартиры с предчистовой отделкой.',
        ]);
    }

    /**
     * Удалить все комплексы, созданные вручную
     */
    public static function cleanManual(): int
    {
        return Complex::whereHas('developer', function ($query) {
            $query->where('source', 'manual');
        })->forceDelete();
    }
}
