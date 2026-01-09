<?php

namespace Database\Factories\Property;

use App\Models\Contact\Contact;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Property\Property;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Currency;
use App\Models\Reference\Dictionary;
use App\Models\Reference\Source;
use App\Models\User;
use Database\Factories\Contact\ContactFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика для создания тестовых объектов недвижимости
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Типы недвижимости для квартир (80% объектов)
     */
    private array $apartmentTypes = ['Квартира', 'Пентхаус', 'Квартира на земле', 'Студия'];

    /**
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        // Получаем существующие данные из БД
        $user = User::inRandomOrder()->first();
        $currency = Currency::where('is_active', true)->inRandomOrder()->first();

        // Начинаем с улицы и идём вверх по иерархии (так гарантируем валидные связи)
        $street = Street::inRandomOrder()->first();
        $zone = $street ? Zone::find($street->zone_id) : null;
        $district = $street ? District::find($street->district_id) : null;
        $city = $street ? City::find($street->city_id) : null;
        $state = $city ? State::find($city->state_id) : null;
        $country = $state ? Country::find($state->country_id) : null;

        // Комплекс и секция/корпус
        $complex = $this->faker->boolean(20) ? Complex::where('is_active', true)->inRandomOrder()->first() : null;
        $block = $complex ? Block::where('complex_id', $complex->id)->inRandomOrder()->first() : null;

        // 80% - Продажа квартир, 20% - случайный тип
        $isApartmentSale = $this->faker->boolean(80);

        if ($isApartmentSale) {
            // Продажа квартир
            $dealType = Dictionary::where('type', 'deal_type')
                ->where('name', 'like', '%Продажа квартир%')
                ->first();

            // Тип недвижимости: Квартира, Пентхаус, Квартира на земле, Студия
            $propertyType = Dictionary::where('type', 'property_type')
                ->whereIn('name', $this->apartmentTypes)
                ->inRandomOrder()
                ->first();
        } else {
            // Случайный тип сделки и недвижимости
            $dealType = Dictionary::where('type', 'deal_type')->inRandomOrder()->first();
            $propertyType = Dictionary::where('type', 'property_type')->inRandomOrder()->first();
        }

        // Остальные справочники
        $dealKind = Dictionary::where('type', 'deal_kind')->inRandomOrder()->first();
        $buildingType = Dictionary::where('type', 'building_type')->inRandomOrder()->first();
        $condition = Dictionary::where('type', 'condition')->inRandomOrder()->first();
        $wallType = Dictionary::where('type', 'wall_type')->inRandomOrder()->first();
        $heatingType = Dictionary::where('type', 'heating_type')->inRandomOrder()->first();
        $roomCount = Dictionary::where('type', 'room_count')->inRandomOrder()->first();
        $bathroomCount = Dictionary::where('type', 'bathroom_count')->inRandomOrder()->first();
        $ceilingHeight = Dictionary::where('type', 'ceiling_height')->inRandomOrder()->first();
        $yearBuilt = Dictionary::where('type', 'year_built')->inRandomOrder()->first();

        $floor = $this->faker->numberBetween(1, 25);
        $floorsTotal = $this->faker->numberBetween($floor, 30);

        // Площади и цена
        $areaTotal = $this->faker->randomFloat(2, 25, 300);
        $price = $this->faker->randomFloat(2, 15000, 500000);
        $pricePerM2 = $areaTotal > 0 ? round($price / $areaTotal, 2) : null;

        return [
            // Связи
            'user_id' => $user?->id ?? 1,
            'source_id' => Source::where('is_active', true)->inRandomOrder()->first()?->id,
            'currency_id' => $currency?->id,

            // Комплекс
            'complex_id' => $complex?->id,
            'block_id' => $block?->id,

            // Локация
            'country_id' => $country?->id,
            'state_id' => $state?->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'street_id' => $street?->id,
            'building_number' => $this->faker->boolean(90) ? $this->generateBuildingNumber() : null,
            'apartment_number' => $this->faker->boolean(50) ? (string) $this->faker->numberBetween(1, 200) : null,
            'location_name' => null,
            'latitude' => $city ? $this->faker->latitude(48.0, 52.0) : null,
            'longitude' => $city ? $this->faker->longitude(22.0, 40.0) : null,

            // Справочники
            'deal_type_id' => $dealType?->id,
            'deal_kind_id' => $dealKind?->id,
            'building_type_id' => $buildingType?->id,
            'property_type_id' => $propertyType?->id,
            'condition_id' => $condition?->id,
            'wall_type_id' => $wallType?->id,
            'heating_type_id' => $heatingType?->id,
            'room_count_id' => $roomCount?->id,
            'bathroom_count_id' => $bathroomCount?->id,
            'ceiling_height_id' => $ceilingHeight?->id,

            // Характеристики
            'area_total' => $areaTotal,
            'area_living' => $this->faker->randomFloat(2, 15, 200),
            'area_kitchen' => $this->faker->randomFloat(2, 6, 40),
            'area_land' => $this->faker->boolean(30) ? $this->faker->randomFloat(2, 1, 50) : null,
            'floor' => $floor,
            'floors_total' => $floorsTotal,
            'year_built' => $yearBuilt?->id,

            // Цена
            'price' => $price,
            'price_per_m2' => $pricePerM2,
            'commission' => $this->faker->randomFloat(2, 1, 5),
            'commission_type' => 'percent',

            // Медиа
            'youtube_url' => $this->faker->boolean(10) ? 'https://www.youtube.com/watch?v=' . $this->faker->regexify('[a-zA-Z0-9]{11}') : null,
            'external_url' => null,

            // Настройки
            'is_advertised' => $this->faker->boolean(70),
            'is_visible_to_agents' => $this->faker->boolean(80),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence(10) : null,
            'agent_notes' => $this->faker->boolean(20) ? $this->faker->sentence(5) : null,
            'status' => $this->faker->randomElement(['draft', 'active', 'active', 'active', 'on_review', 'archived']),
        ];
    }

    /**
     * Генерация номера дома (1-99 с опциональной буквой)
     * Примеры: "12", "7а", "45б", "3", "88в"
     */
    private function generateBuildingNumber(): string
    {
        $number = $this->faker->numberBetween(1, 99);

        // 30% шанс добавить букву
        if ($this->faker->boolean(30)) {
            $letters = ['а', 'б', 'в', 'г', 'д'];
            $number .= $this->faker->randomElement($letters);
        }

        return (string) $number;
    }

    /**
     * Объект со статусом "активный"
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Объект со статусом "черновик"
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Объект со статусом "в архиве"
     */
    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Объект продажа квартир (100%)
     */
    public function apartmentSale(): static
    {
        $dealType = Dictionary::where('type', 'deal_type')
            ->where('name', 'like', '%Продажа квартир%')
            ->first();

        $propertyType = Dictionary::where('type', 'property_type')
            ->whereIn('name', $this->apartmentTypes)
            ->inRandomOrder()
            ->first();

        return $this->state(fn(array $attributes) => [
            'deal_type_id' => $dealType?->id ?? $attributes['deal_type_id'],
            'property_type_id' => $propertyType?->id ?? $attributes['property_type_id'],
        ]);
    }

    /**
     * Объект продажа
     */
    public function forSale(): static
    {
        $dealType = Dictionary::where('type', 'deal_type')
            ->where('name', 'like', '%продаж%')
            ->first();

        return $this->state(fn(array $attributes) => [
            'deal_type_id' => $dealType?->id ?? $attributes['deal_type_id'],
        ]);
    }

    /**
     * Объект аренда
     */
    public function forRent(): static
    {
        $dealType = Dictionary::where('type', 'deal_type')
            ->where('name', 'like', '%аренд%')
            ->first();

        return $this->state(fn(array $attributes) => [
            'deal_type_id' => $dealType?->id ?? $attributes['deal_type_id'],
        ]);
    }

    /**
     * Премиум объект (высокая цена)
     */
    public function premium(): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, 200000, 2000000),
            'area_total' => $this->faker->randomFloat(2, 100, 500),
        ]);
    }

    /**
     * Создание объекта с переводами
     */
    public function withTranslations(): static
    {
        return $this->afterCreating(function (Property $property) {
            PropertyTranslationFactory::new()
                ->count(3)
                ->sequence(
                    ['locale' => 'ru'],
                    ['locale' => 'ua'],
                    ['locale' => 'en']
                )
                ->create(['property_id' => $property->id]);
        });
    }

    /**
     * Создание объекта с контактами (создает новые контакты)
     */
    public function withContacts(int $count = 1): static
    {
        return $this->afterCreating(function (Property $property) use ($count) {
            $contacts = ContactFactory::new()
                ->count($count)
                ->withPhones(2)
                ->create();

            $property->contacts()->attach($contacts->pluck('id'));
        });
    }

    /**
     * Привязать существующие контакты к объекту
     */
    public function withExistingContacts(int $count = 1): static
    {
        return $this->afterCreating(function (Property $property) use ($count) {
            $contacts = Contact::inRandomOrder()->limit($count)->get();

            if ($contacts->isNotEmpty()) {
                $property->contacts()->attach($contacts->pluck('id'));
            }
        });
    }

    /**
     * Полный объект со всеми связями
     */
    public function complete(): static
    {
        return $this
            ->withTranslations()
            ->withContacts(1);
    }
}
