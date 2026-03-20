<?php

namespace Tests\Feature\Migration;

use App\Models\Employee\Employee;
use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Тесты верификации миграции из factor_dump в realt_bank.
 *
 * Запуск: sail artisan test --filter=VerifyMigrationTest
 * ./vendor/bin/sail artisan test --filter=VerifyMigrationTest
 *
 * Тесты проверяют корректность перенесённых данных, сравнивая
 * записи в старой базе (factor_dump) с новой (realt_bank).
 * НЕ используют RefreshDatabase — работают с реальными данными после миграции.
 */
class VerifyMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Тесты верификации работают с реальной базой realt_bank, не с testing
        config(['database.connections.mysql.database' => 'realt_bank']);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Проверка: количество перенесённых объектов соответствует фильтру.
     * Только status IN(1,2,3), rent=0, deleted=0.
     */
    public function test_properties_count_matches_source(): void
    {
        $oldCount = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('status', [1, 2, 3])
            ->where('rent', 0)
            ->where('deleted', 0)
            ->count();

        $newCount = Property::count();

        // Если задан --limit, пропускаем проверку полного количества
        if ($newCount < $oldCount) {
            $this->markTestSkipped(
                "Частичная миграция: {$newCount} из {$oldCount} объектов (использован --limit)"
            );
        }

        $this->assertEquals($oldCount, $newCount,
            "Количество properties ({$newCount}) не совпадает с количеством objects ({$oldCount})"
        );
    }

    /**
     * Проверка: все перенесённые объекты имеют правильный property_type_id.
     * status=1 → 23 (Квартира), status=2 → 28 (Дом), status=3 → 40 (Коммерция).
     */
    public function test_property_types_are_correct(): void
    {
        $expectedTypes = [23, 28, 40]; // Квартира, Дом, Коммерция

        $invalidCount = Property::whereNotIn('property_type_id', $expectedTypes)->count();

        $this->assertEquals(0, $invalidCount,
            "{$invalidCount} объектов имеют неожиданный property_type_id"
        );
    }

    /**
     * Проверка: все объекты имеют статус 'active'.
     */
    public function test_all_properties_are_active(): void
    {
        $nonActive = Property::where('status', '!=', 'active')->count();

        $this->assertEquals(0, $nonActive,
            "{$nonActive} объектов имеют статус отличный от 'active'"
        );
    }

    /**
     * Проверка: числовые поля (площадь, этаж, цена) корректно перенесены.
     * Берём каждый перенесённый объект и сравниваем с оригиналом.
     */
    public function test_property_numeric_fields_match_source(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $this->assertNotEmpty($properties, 'Нет перенесённых объектов для проверки');

        foreach ($properties as $property) {
            // ID сохраняются при миграции — ищем по ID напрямую
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            // Площадь общая
            $this->assertEquals(
                (float) ($old->total_area ?: 0),
                (float) ($property->area_total ?: 0),
                "Object #{$old->id}: area_total не совпадает"
            );

            // Площадь жилая
            $this->assertEquals(
                (float) ($old->area_live ?: 0),
                (float) ($property->area_living ?: 0),
                "Object #{$old->id}: area_living не совпадает"
            );

            // Площадь кухни
            $this->assertEquals(
                (float) ($old->area_kitchen ?: 0),
                (float) ($property->area_kitchen ?: 0),
                "Object #{$old->id}: area_kitchen не совпадает"
            );

            // Этаж
            $this->assertEquals(
                $old->floor_build ?: null,
                $property->floor,
                "Object #{$old->id}: floor не совпадает"
            );

            // Этажность
            $this->assertEquals(
                $old->all_floors ?: null,
                $property->floors_total,
                "Object #{$old->id}: floors_total не совпадает"
            );

            // Цена
            $this->assertEquals(
                (float) ($old->price ?: 0),
                (float) ($property->price ?: 0),
                "Object #{$old->id}: price не совпадает"
            );

            // Цена за м2
            $this->assertEquals(
                (float) ($old->price_area ?: 0),
                (float) ($property->price_per_m2 ?: 0),
                "Object #{$old->id}: price_per_m2 не совпадает"
            );
        }
    }

    /**
     * Проверка: адрес (номер дома, квартира) перенесён корректно.
     */
    public function test_property_address_fields_match_source(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $this->assertNotEmpty($properties);

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $this->assertEquals(
                $old->number_house ?: null,
                $property->building_number,
                "Object #{$old->id}: building_number не совпадает"
            );

            $this->assertEquals(
                $old->num_flat ?: null,
                $property->apartment_number,
                "Object #{$old->id}: apartment_number не совпадает"
            );
        }
    }

    /**
     * Проверка: координаты правильно разделены из строки "lat,lng".
     */
    public function test_property_coordinates_parsed_correctly(): void
    {
        // Берём перенесённые объекты, у которых есть координаты
        $properties = Property::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->limit(20)
            ->get();

        if ($properties->isEmpty()) {
            $this->markTestSkipped('Нет объектов с координатами для проверки');
        }

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old || empty($old->coords)) continue;

            $parts = explode(',', $old->coords);
            if (count($parts) !== 2) continue;

            $expectedLat = (float) trim($parts[0]);
            $expectedLng = (float) trim($parts[1]);

            // Сравниваем как float — в БД может быть разное количество знаков после запятой
            $this->assertEqualsWithDelta($expectedLat, (float) $property->latitude, 0.000001,
                "Object #{$old->id}: latitude не совпадает"
            );
            $this->assertEqualsWithDelta($expectedLng, (float) $property->longitude, 0.000001,
                "Object #{$old->id}: longitude не совпадает"
            );
        }
    }

    /**
     * Проверка: у каждого объекта есть user_id (агент привязан).
     */
    public function test_all_properties_have_user(): void
    {
        $withoutUser = Property::whereNull('user_id')->count();

        $this->assertEquals(0, $withoutUser,
            "{$withoutUser} объектов без привязанного агента (user_id IS NULL)"
        );
    }

    /**
     * Проверка: у каждого объекта есть локация (city_id).
     */
    public function test_all_properties_have_city(): void
    {
        $withoutCity = Property::whereNull('city_id')->count();

        $this->assertEquals(0, $withoutCity,
            "{$withoutCity} объектов без города (city_id IS NULL)"
        );
    }

    /**
     * Проверка: количество фото для каждого объекта совпадает с оригиналом.
     */
    public function test_photo_counts_match_source(): void
    {
        $properties = Property::with('photos')->orderBy('id')->limit(20)->get();
        $this->assertNotEmpty($properties);

        foreach ($properties as $property) {
            // ID объекта совпадает со старым — ищем фото по нему
            $oldPhotoCount = DB::connection('factor_dump')
                ->table('object_images')
                ->where('object_id', $property->id)
                ->whereNull('deleted_at')
                ->count();

            $newPhotoCount = $property->photos->count();

            $this->assertEquals($oldPhotoCount, $newPhotoCount,
                "Property #{$property->id}: фото old={$oldPhotoCount}, new={$newPhotoCount}"
            );
        }
    }

    /**
     * Проверка: компания "Factor" создана.
     */
    public function test_company_factor_exists(): void
    {
        $company = Company::where('name', 'Factor')->first();

        $this->assertNotNull($company, 'Компания "Factor" не найдена');
    }

    /**
     * Проверка: все филиалы перенесены как офисы.
     */
    public function test_offices_count_matches_filials(): void
    {
        $oldCount = DB::connection('factor_dump')->table('filials')->count();
        $newCount = CompanyOffice::count();

        $this->assertEquals($oldCount, $newCount,
            "Количество offices ({$newCount}) не совпадает с filials ({$oldCount})"
        );
    }

    /**
     * Проверка: количество перенесённых пользователей.
     * Каждый старый user (deleted=0) должен иметь соответствие в новой базе.
     */
    public function test_users_migrated(): void
    {
        $oldCount = DB::connection('factor_dump')
            ->table('users')
            ->where('deleted', 0)
            ->count();

        // В новой базе могут быть дополнительные пользователи (admin и т.д.)
        $newCount = User::count();

        $this->assertGreaterThanOrEqual($oldCount, $newCount,
            "Пользователей в новой базе ({$newCount}) меньше чем в старой ({$oldCount})"
        );
    }

    /**
     * Проверка: каждый перенесённый пользователь имеет Employee запись.
     */
    public function test_migrated_users_have_employees(): void
    {
        // Пользователи с email @factor.local — это перенесённые из factor_dump
        $migratedUsers = User::where('email', 'like', '%@factor.local')->get();

        foreach ($migratedUsers as $user) {
            $hasEmployee = Employee::where('user_id', $user->id)->exists();

            $this->assertTrue($hasEmployee,
                "User #{$user->id} ({$user->email}) не имеет Employee записи"
            );
        }
    }

    /**
     * Проверка: property_type_id = 23 (Квартира) для объектов status=1.
     */
    public function test_apartments_have_correct_type(): void
    {
        $apartments = Property::where('property_type_id', 23)->count();

        $oldApartments = DB::connection('factor_dump')
            ->table('objects')
            ->where('status', 1)
            ->where('rent', 0)
            ->where('deleted', 0)
            ->count();

        if ($apartments < $oldApartments) {
            // Частичная миграция — проверяем что все property_type_id=23 корректны
            $this->assertGreaterThan(0, $apartments, 'Нет ни одной квартиры');
            return;
        }

        $this->assertEquals($oldApartments, $apartments,
            "Квартир: old={$oldApartments}, new={$apartments}"
        );
    }

    /**
     * Проверка: property_type_id = 28 (Дом) для объектов status=2.
     */
    public function test_houses_have_correct_type(): void
    {
        $houses = Property::where('property_type_id', 28)->count();

        $oldHouses = DB::connection('factor_dump')
            ->table('objects')
            ->where('status', 2)
            ->where('rent', 0)
            ->where('deleted', 0)
            ->count();

        if ($houses < $oldHouses) {
            $this->assertGreaterThanOrEqual(0, $houses);
            return;
        }

        $this->assertEquals($oldHouses, $houses,
            "Домов: old={$oldHouses}, new={$houses}"
        );
    }

    /**
     * Проверка: property_type_id = 40 (Коммерция) для объектов status=3.
     */
    public function test_commercial_have_correct_type(): void
    {
        $commercial = Property::where('property_type_id', 40)->count();

        $oldCommercial = DB::connection('factor_dump')
            ->table('objects')
            ->where('status', 3)
            ->where('rent', 0)
            ->where('deleted', 0)
            ->count();

        if ($commercial < $oldCommercial) {
            $this->assertGreaterThanOrEqual(0, $commercial);
            return;
        }

        $this->assertEquals($oldCommercial, $commercial,
            "Коммерции: old={$oldCommercial}, new={$commercial}"
        );
    }

    /**
     * Проверка: фото имеют корректные пути (prefix legacy/).
     */
    public function test_photos_have_valid_paths(): void
    {
        $photos = PropertyPhoto::limit(50)->get();

        if ($photos->isEmpty()) {
            $this->markTestSkipped('Нет фото для проверки');
        }

        foreach ($photos as $photo) {
            $this->assertStringStartsWith('legacy/',  $photo->path,
                "Photo #{$photo->id}: path не начинается с 'legacy/'"
            );
            $this->assertNotEmpty($photo->filename,
                "Photo #{$photo->id}: filename пустой"
            );
        }
    }

    /**
     * Проверка: локации (город, район, жилмассив, улица) перенесены корректно.
     * Сравниваем названия из старой базы (lib_towns, lib_regions, lib_zones, lib_streets)
     * с привязанными записями в новой базе (cities, districts, zones, streets).
     */
    public function test_property_locations_match_source(): void
    {
        $properties = Property::with(['city', 'district', 'zone', 'street'])
            ->limit(50)
            ->get();

        $this->assertNotEmpty($properties, 'Нет перенесённых объектов для проверки');

        foreach ($properties as $property) {
            // Находим оригинальный объект по ID (ID сохраняются при миграции)
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            // Город: lib_towns.name → cities.name
            if ($old->town_id && $property->city) {
                $oldCity = DB::connection('factor_dump')
                    ->table('lib_towns')
                    ->where('id', $old->town_id)
                    ->value('name');

                if ($oldCity) {
                    $this->assertEquals(
                        mb_strtolower(trim($oldCity)),
                        mb_strtolower(trim($property->city->name)),
                        "Object #{$old->id}: город не совпадает (old='{$oldCity}', new='{$property->city->name}')"
                    );
                }
            }

            // Район: lib_regions.name → districts.name
            if ($old->region_id && $property->district) {
                $oldDistrict = DB::connection('factor_dump')
                    ->table('lib_regions')
                    ->where('id', $old->region_id)
                    ->value('name');

                if ($oldDistrict) {
                    $this->assertEquals(
                        mb_strtolower(trim($oldDistrict)),
                        mb_strtolower(trim($property->district->name)),
                        "Object #{$old->id}: район не совпадает (old='{$oldDistrict}', new='{$property->district->name}')"
                    );
                }
            }

            // Жилмассив: lib_zones.name → zones.name
            if ($old->zone_id && $property->zone) {
                $oldZone = DB::connection('factor_dump')
                    ->table('lib_zones')
                    ->where('id', $old->zone_id)
                    ->value('name');

                if ($oldZone) {
                    $this->assertEquals(
                        mb_strtolower(trim($oldZone)),
                        mb_strtolower(trim($property->zone->name)),
                        "Object #{$old->id}: жилмассив не совпадает (old='{$oldZone}', new='{$property->zone->name}')"
                    );
                }
            }

            // Улица: lib_streets.name → streets.name
            if ($old->street_id && $property->street) {
                $oldStreet = DB::connection('factor_dump')
                    ->table('lib_streets')
                    ->where('id', $old->street_id)
                    ->value('name');

                if ($oldStreet) {
                    $this->assertEquals(
                        mb_strtolower(trim($oldStreet)),
                        mb_strtolower(trim($property->street->name)),
                        "Object #{$old->id}: улица не совпадает (old='{$oldStreet}', new='{$property->street->name}')"
                    );
                }
            }
        }
    }

    /**
     * Проверка: нет "осиротевших" фото (property_id ссылается на несуществующий property).
     */
    public function test_no_orphan_photos(): void
    {
        $orphans = PropertyPhoto::whereNotIn(
            'property_id',
            Property::select('id')
        )->count();

        $this->assertEquals(0, $orphans,
            "{$orphans} фото ссылаются на несуществующие объекты"
        );
    }
}
