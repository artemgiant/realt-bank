<?php

namespace Database\Factories\Property;

use App\Models\Contact\Contact;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Location\Street;
use App\Models\Property\Property;
use App\Models\Reference\Complex;
use App\Models\Reference\Currency;
use App\Models\Reference\Dictionary;
use App\Models\Reference\Source;
use App\Models\User;
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
     * Определение состояния модели по умолчанию
     */
    public function definition(): array
    {
        // Получаем существующие данные из БД
        $user = User::inRandomOrder()->first();
        $currency = Currency::where('is_active', true)->inRandomOrder()->first();
        $country = Country::where('is_active', true)->inRandomOrder()->first();
        $region = $country ? Region::where('country_id', $country->id)->inRandomOrder()->first() : null;
        $city = $region ? City::where('region_id', $region->id)->inRandomOrder()->first() : null;
        $district = $city ? District::where('city_id', $city->id)->inRandomOrder()->first() : null;
        $street = $city ? Street::where('city_id', $city->id)->inRandomOrder()->first() : null;

        // Справочники
        $dealType = Dictionary::where('type', 'deal_type')->inRandomOrder()->first();
        $dealKind = Dictionary::where('type', 'deal_kind')->inRandomOrder()->first();
        $propertyType = Dictionary::where('type', 'property_type')->inRandomOrder()->first();
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

        return [
            // Связи
            'user_id' => $user?->id ?? 1,
            'contact_id' => null,
            'source_id' => Source::where('is_active', true)->inRandomOrder()->first()?->id,
            'currency_id' => $currency?->id,

            // Комплекс
            'complex_id' => $this->faker->boolean(20) ? Complex::where('is_active', true)->inRandomOrder()->first()?->id : null,
            'section_id' => null,

            // Локация
            'country_id' => $country?->id,
            'region_id' => $region?->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => null,
            'street_id' => $street?->id,
            'landmark_id' => null,
            'building_number' => $this->faker->boolean(80) ? $this->faker->buildingNumber() : null,
            'apartment_number' => $this->faker->boolean(50) ? $this->faker->numberBetween(1, 200) : null,
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
            'area_total' => $this->faker->randomFloat(2, 25, 300),
            'area_living' => $this->faker->randomFloat(2, 15, 200),
            'area_kitchen' => $this->faker->randomFloat(2, 6, 40),
            'area_land' => $this->faker->boolean(30) ? $this->faker->randomFloat(2, 1, 50) : null,
            'floor' => $floor,
            'floors_total' => $floorsTotal,
            'year_built' => $yearBuilt?->id,

            // Цена
            'price' => $this->faker->randomFloat(2, 15000, 500000),
            'commission' => $this->faker->randomFloat(2, 1, 5),
            'commission_type' => 'percent',

            // Медиа
            'youtube_url' => $this->faker->boolean(10) ? 'https://www.youtube.com/watch?v=' . $this->faker->regexify('[a-zA-Z0-9]{11}') : null,
            'external_url' => null,

            // Настройки
            'is_visible_to_agents' => $this->faker->boolean(80),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence(10) : null,
            'agent_notes' => $this->faker->boolean(20) ? $this->faker->sentence(5) : null,
            'status' => $this->faker->randomElement(['draft', 'active', 'active', 'active', 'on_review', 'archived']),
        ];
    }

    /**
     * Объект со статусом "активный"
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Объект со статусом "черновик"
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Объект со статусом "в архиве"
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Объект с контактом
     */
    public function withContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_id' => Contact::inRandomOrder()->first()?->id,
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

        return $this->state(fn (array $attributes) => [
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

        return $this->state(fn (array $attributes) => [
            'deal_type_id' => $dealType?->id ?? $attributes['deal_type_id'],
        ]);
    }

    /**
     * Премиум объект (высокая цена)
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
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
     * Создание объекта с фото
     */
    public function withPhotos(int $count = 3): static
    {
        return $this->afterCreating(function (Property $property) use ($count) {
            PropertyPhotoFactory::new()
                ->count($count)
                ->sequence(fn ($sequence) => [
                    'sort_order' => $sequence->index + 1,
                    'is_main' => $sequence->index === 0,
                ])
                ->create(['property_id' => $property->id]);
        });
    }

    /**
     * Создание объекта с документами
     */
    public function withDocuments(int $count = 2): static
    {
        return $this->afterCreating(function (Property $property) use ($count) {
            PropertyDocumentFactory::new()
                ->count($count)
                ->create(['property_id' => $property->id]);
        });
    }

    /**
     * Полный объект со всеми связями
     */
    public function complete(): static
    {
        return $this
            ->withContact()
            ->withTranslations()
            ->withPhotos(5)
            ->withDocuments(2);
    }
}
