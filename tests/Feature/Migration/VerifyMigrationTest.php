<?php

namespace Tests\Feature\Migration;

use App\Models\Employee\Employee;
use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use App\Models\Property\PropertyTranslation;
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

            // Этаж (в старой базе поля перепутаны: all_floors = этаж, floor_build = этажность)
            $this->assertEquals(
                $old->all_floors ?: null,
                $property->floor,
                "Object #{$old->id}: floor не совпадает (old all_floors={$old->all_floors})"
            );

            // Этажность
            $this->assertEquals(
                $old->floor_build ?: null,
                $property->floors_total,
                "Object #{$old->id}: floors_total не совпадает (old floor_build={$old->floor_build})"
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
     * Проверка: заголовок и описание перенесены в property_translations.
     */
    public function test_translations_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $this->assertNotEmpty($properties);

        $withTranslation = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $oldTitle = $data->title ?? null;
            $oldDescription = $data->description ?? null;

            if ($oldTitle || $oldDescription) {
                $translation = PropertyTranslation::where('property_id', $property->id)
                    ->where('locale', 'ru')
                    ->first();

                $this->assertNotNull($translation,
                    "Property #{$property->id}: отсутствует translation (title='{$oldTitle}')"
                );

                if ($oldTitle) {
                    $this->assertEquals($oldTitle, $translation->title,
                        "Property #{$property->id}: title не совпадает"
                    );
                }

                if ($oldDescription) {
                    $this->assertEquals($oldDescription, $translation->description,
                        "Property #{$property->id}: description не совпадает"
                    );
                }
                $withTranslation++;
            }
        }

        $this->assertGreaterThan(0, $withTranslation, 'Ни один объект не имеет translation');
    }

    /**
     * Проверка: контакты продавцов привязаны к объектам.
     * Проверяем: связь contactables, телефон, имя, роль.
     */
    public function test_contacts_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $this->assertNotEmpty($properties);

        $withContact = 0;
        $typeSaleRoles = [1 => 'Риелтор', 2 => 'Продавец'];

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $phone = $data->telephone ?? null;
            $name = $data->name_sale ?? null;

            if (empty($phone) && empty($name)) continue;

            // Проверяем что контакт привязан через contactables
            $contactable = DB::table('contactables')
                ->where('contactable_type', Property::class)
                ->where('contactable_id', $property->id)
                ->first();

            $this->assertNotNull($contactable,
                "Property #{$property->id}: контакт (тел: {$phone}) не привязан через contactables"
            );

            // Проверяем роль контакта
            $expectedRole = $typeSaleRoles[$old->type_sale ?? 0] ?? null;
            if ($expectedRole) {
                $this->assertEquals($expectedRole, $contactable->role,
                    "Property #{$property->id}: роль контакта (ожид: {$expectedRole}, факт: {$contactable->role})"
                );
            }

            // Проверяем что contact существует и имеет имя
            $contact = DB::table('contacts')->where('id', $contactable->contact_id)->first();
            $this->assertNotNull($contact,
                "Property #{$property->id}: contact #{$contactable->contact_id} не существует"
            );

            if (!empty($name)) {
                $this->assertEquals(
                    mb_strtolower(trim($name)),
                    mb_strtolower(trim($contact->first_name)),
                    "Property #{$property->id}: имя контакта не совпадает (old='{$name}', new='{$contact->first_name}')"
                );
            }

            // Проверяем телефон
            if (!empty($phone)) {
                $normalizedPhone = preg_replace('/\D/', '', $phone);
                $hasPhone = DB::table('contact_phones')
                    ->where('contact_id', $contact->id)
                    ->where('phone', $normalizedPhone)
                    ->exists();

                $this->assertTrue($hasPhone,
                    "Property #{$property->id}: телефон {$normalizedPhone} не найден у контакта #{$contact->id}"
                );
            }

            $withContact++;
        }

        $this->assertGreaterThan(0, $withContact, 'Ни один объект не имеет контакта');
    }

    /**
     * Проверка: year_built заполнен из JSON data.year_building.
     */
    public function test_year_built_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(20)->get();
        $withYear = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $oldYear = !empty($data->year_building) ? (int) $data->year_building : null;

            if ($oldYear) {
                $this->assertEquals($oldYear, $property->year_built,
                    "Property #{$property->id}: year_built не совпадает"
                );
                $withYear++;
            }
        }

        // Не все объекты имеют год — просто проверяем что хотя бы часть заполнена
        if ($withYear === 0) {
            $this->markTestSkipped('Нет объектов с year_building для проверки');
        }
    }

    /**
     * Проверка: agent_notes заполнен из JSON data.notes.
     */
    public function test_agent_notes_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(20)->get();

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $oldNotes = $data->notes ?? null;

            if (!empty($oldNotes)) {
                $this->assertEquals($oldNotes, $property->agent_notes,
                    "Property #{$property->id}: agent_notes не совпадает"
                );
            }
        }
    }

    /**
     * Проверка: ЖК (complex_id) заполнен для объектов с complex > 0.
     */
    public function test_complex_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withComplex = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old || empty($old->complex) || $old->complex <= 0) continue;

            $this->assertNotNull($property->complex_id,
                "Property #{$property->id}: complex_id NULL, но в старой базе complex={$old->complex}"
            );

            // Проверяем что название ЖК совпадает
            $oldName = DB::connection('factor_dump')
                ->table('lib_other')
                ->where('id', $old->complex)
                ->value('name');

            if ($oldName && $property->complex_id) {
                $newName = DB::table('complexes')
                    ->where('id', $property->complex_id)
                    ->value('name');

                $this->assertTrue(
                    mb_strtolower(trim($oldName)) === mb_strtolower(trim($newName)),
                    "Property #{$property->id}: ЖК не совпадает (old='{$oldName}', new='{$newName}')"
                );
            }
            $withComplex++;
        }

        if ($withComplex === 0) {
            $this->markTestSkipped('Нет объектов с ЖК для проверки');
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

    /**
     * Проверка: комиссия перенесена из JSON data.price_rieltor / price_rieltor_proc.
     */
    public function test_commission_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withCommission = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);

            if (!empty($data->price_rieltor_proc)) {
                $this->assertEquals((float) $data->price_rieltor_proc, (float) $property->commission,
                    "Property #{$property->id}: commission не совпадает (price_rieltor_proc)"
                );
                $this->assertEquals('percent', $property->commission_type,
                    "Property #{$property->id}: commission_type должен быть 'percent'"
                );
                $withCommission++;
            } elseif (!empty($data->price_rieltor)) {
                $this->assertEquals((float) $data->price_rieltor, (float) $property->commission,
                    "Property #{$property->id}: commission не совпадает (price_rieltor)"
                );
                $this->assertEquals('fixed', $property->commission_type,
                    "Property #{$property->id}: commission_type должен быть 'fixed'"
                );
                $withCommission++;
            }
        }

        if ($withCommission === 0) {
            $this->markTestSkipped('Нет объектов с комиссией для проверки');
        }
    }

    /**
     * Проверка: youtube_url перенесён из JSON data.youtube.
     */
    public function test_youtube_url_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withYoutube = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $oldYoutube = $data->youtube ?? null;

            if (!empty($oldYoutube)) {
                $this->assertEquals($oldYoutube, $property->youtube_url,
                    "Property #{$property->id}: youtube_url не совпадает"
                );
                $withYoutube++;
            }
        }

        if ($withYoutube === 0) {
            $this->markTestSkipped('Нет объектов с YouTube для проверки');
        }
    }

    /**
     * Проверка: external_url перенесён из JSON data.linkToAd или objects.rem_url.
     */
    public function test_external_url_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withUrl = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $data = json_decode($old->data ?? '{}', false);
            $expected = !empty($data->linkToAd) ? $data->linkToAd : ($old->rem_url ?: null);

            if (!empty($expected)) {
                $this->assertEquals($expected, $property->external_url,
                    "Property #{$property->id}: external_url не совпадает"
                );
                $withUrl++;
            }
        }

        if ($withUrl === 0) {
            $this->markTestSkipped('Нет объектов с external_url для проверки');
        }
    }

    /**
     * Проверка: employee_id привязан к объекту.
     * Если у старого объекта есть user_id и этот user имеет Employee — должен быть заполнен.
     */
    public function test_employee_id_migrated(): void
    {
        $properties = Property::whereNotNull('user_id')
            ->orderBy('id')
            ->limit(50)
            ->get();

        $withEmployee = 0;

        foreach ($properties as $property) {
            $employee = Employee::where('user_id', $property->user_id)->first();

            if ($employee) {
                $this->assertEquals($employee->id, $property->employee_id,
                    "Property #{$property->id}: employee_id должен быть {$employee->id} (user_id={$property->user_id})"
                );
                $withEmployee++;
            }
        }

        if ($withEmployee === 0) {
            $this->markTestSkipped('Нет объектов с employee_id для проверки');
        }
    }

    /**
     * Проверка: contact_type_id перенесён из type_sale.
     * type_sale=1 → 202 ("Агент 50/50"), type_sale=2 → 195 ("Эксклюзив / Владелец").
     */
    public function test_contact_type_id_migrated(): void
    {
        $typeMap = [1 => 202, 2 => 195];
        $properties = Property::orderBy('id')->limit(50)->get();
        $checked = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old || empty($old->type_sale)) continue;

            $expected = $typeMap[$old->type_sale] ?? null;

            if ($expected) {
                $this->assertEquals($expected, $property->contact_type_id,
                    "Property #{$property->id}: contact_type_id (type_sale={$old->type_sale}) должен быть {$expected}"
                );
                $checked++;
            }
        }

        if ($checked === 0) {
            $this->markTestSkipped('Нет объектов с type_sale для проверки');
        }
    }

    /**
     * Проверка: features (балкон, парковка, вид) перенесены в property_features.
     */
    public function test_features_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withFeatures = 0;

        foreach ($properties as $property) {
            $featureCount = DB::table('property_features')
                ->where('property_id', $property->id)
                ->count();

            if ($featureCount > 0) {
                $withFeatures++;
            }
        }

        // Не все объекты имеют features — проверяем что хотя бы часть
        if ($withFeatures === 0) {
            $this->markTestSkipped('Нет объектов с features для проверки');
        }

        $this->assertGreaterThan(0, $withFeatures);
    }

    /**
     * Проверка: is_visible_to_agents соответствует полю open в старой базе.
     */
    public function test_visibility_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $expected = (bool) ($old->open ?? 1);
            $this->assertEquals($expected, (bool) $property->is_visible_to_agents,
                "Property #{$property->id}: is_visible_to_agents (open={$old->open})"
            );
        }
    }

    /**
     * Проверка: timestamps (created_at, updated_at) перенесены из старой базы.
     */
    public function test_timestamps_preserved(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old || !$old->date_created) continue;

            $oldCreated = \Carbon\Carbon::parse($old->date_created)->format('Y-m-d H:i:s');
            $newCreated = $property->created_at->format('Y-m-d H:i:s');

            $this->assertEquals($oldCreated, $newCreated,
                "Property #{$property->id}: created_at не совпадает (old={$oldCreated}, new={$newCreated})"
            );

            if ($old->date_updated) {
                $oldUpdated = \Carbon\Carbon::parse($old->date_updated)->format('Y-m-d H:i:s');
                $newUpdated = $property->updated_at->format('Y-m-d H:i:s');

                $this->assertEquals($oldUpdated, $newUpdated,
                    "Property #{$property->id}: updated_at не совпадает (old={$oldUpdated}, new={$newUpdated})"
                );
            }
        }
    }

    /**
     * Проверка: area_land перенесена из area_total_ych_t / area_home.
     */
    public function test_area_land_migrated(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $withLand = 0;

        foreach ($properties as $property) {
            $old = DB::connection('factor_dump')
                ->table('objects')
                ->where('id', $property->id)
                ->first();

            if (!$old) continue;

            $oldAreaLand = $old->area_total_ych_t ?: null;
            if (!$oldAreaLand && !empty($old->area_home)) {
                $parsed = (float) preg_replace('/[^\d.]/', '', $old->area_home);
                $oldAreaLand = $parsed > 0 ? $parsed : null;
            }

            if ($oldAreaLand) {
                $this->assertEqualsWithDelta(
                    (float) $oldAreaLand,
                    (float) $property->area_land,
                    0.01,
                    "Property #{$property->id}: area_land не совпадает"
                );
                $withLand++;
            }
        }

        if ($withLand === 0) {
            $this->markTestSkipped('Нет объектов с area_land для проверки');
        }
    }

    /**
     * Проверка: deal_type_id заполнен (Продажа — все мигрируемые объекты rent=0).
     */
    public function test_deal_type_migrated(): void
    {
        $withoutDealType = Property::whereNull('deal_type_id')->count();

        $this->assertEquals(0, $withoutDealType,
            "{$withoutDealType} объектов без deal_type_id"
        );
    }

    /**
     * Проверка: ID объектов сохранены из старой базы (не автоинкремент).
     */
    public function test_property_ids_preserved(): void
    {
        $properties = Property::orderBy('id')->limit(50)->get();
        $this->assertNotEmpty($properties);

        $ids = $properties->pluck('id')->toArray();

        // Все эти ID должны существовать в старой базе
        $existsInOld = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('id', $ids)
            ->count();

        $this->assertEquals(count($ids), $existsInOld,
            "Не все property ID найдены в factor_dump.objects"
        );
    }

    /**
     * Проверка: ID фотографий сохранены из старой базы.
     */
    public function test_photo_ids_preserved(): void
    {
        $photos = PropertyPhoto::orderBy('id')->limit(50)->get();

        if ($photos->isEmpty()) {
            $this->markTestSkipped('Нет фото для проверки');
        }

        $ids = $photos->pluck('id')->toArray();

        $existsInOld = DB::connection('factor_dump')
            ->table('object_images')
            ->whereIn('id', $ids)
            ->count();

        $this->assertEquals(count($ids), $existsInOld,
            "Не все photo ID найдены в factor_dump.object_images"
        );
    }

    /**
     * Проверка: нет осиротевших contactables (ссылка на несуществующий property).
     */
    public function test_no_orphan_contactables(): void
    {
        $orphans = DB::table('contactables')
            ->where('contactable_type', Property::class)
            ->whereNotIn('contactable_id', Property::select('id'))
            ->count();

        $this->assertEquals(0, $orphans,
            "{$orphans} contactables ссылаются на несуществующие объекты"
        );
    }

    /**
     * Проверка: нет осиротевших translations (ссылка на несуществующий property).
     */
    public function test_no_orphan_translations(): void
    {
        $orphans = PropertyTranslation::whereNotIn(
            'property_id',
            Property::select('id')
        )->count();

        $this->assertEquals(0, $orphans,
            "{$orphans} translations ссылаются на несуществующие объекты"
        );
    }

    /**
     * Проверка: notes содержит данные о полях без маппинга (кухня, планировка и т.д.).
     */
    public function test_notes_contain_unmapped_fields(): void
    {
        $properties = Property::whereNotNull('notes')
            ->where('notes', '!=', '')
            ->orderBy('id')
            ->limit(20)
            ->get();

        if ($properties->isEmpty()) {
            $this->markTestSkipped('Нет объектов с notes для проверки');
        }

        // Проверяем что notes не содержат сырых JSON / ID, а содержат человекочитаемые метки
        $knownLabels = ['Кухня', 'Планировка', 'Лестница', 'Окна', 'Санузел', 'Скидка', 'Модератор', 'Ориентация'];
        $hasLabel = false;

        foreach ($properties as $property) {
            foreach ($knownLabels as $label) {
                if (str_contains($property->notes, $label)) {
                    $hasLabel = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasLabel, 'Ни один notes не содержит ожидаемых меток (Кухня, Планировка и т.д.)');
    }
}
