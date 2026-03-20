<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Property\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

/**
 * Контроллер страницы верификации миграции.
 * Визуальное сравнение записей из старой базы (factor_dump) и новой (realt_bank).
 */
class MigrationVerifyController extends Controller
{
    /**
     * Главная страница — список перенесённых объектов с данными из обеих баз.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $typeFilter = $request->input('type'); // 23=квартиры, 28=дома, 40=коммерция
        $statusFilter = $request->input('status'); // ok, mismatch

        // Берём объекты из новой базы
        $query = Property::with(['city', 'district', 'zone', 'street', 'photos'])
            ->orderBy('id');

        if ($typeFilter) {
            $query->where('property_type_id', $typeFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('building_number', 'like', "%{$search}%")
                    ->orWhere('apartment_number', 'like', "%{$search}%");
            });
        }

        $properties = $query->paginate($perPage);

        // Подгружаем старые записи для текущей страницы
        $oldIds = $properties->pluck('id')->toArray();
        $oldObjects = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('id', $oldIds)
            ->get()
            ->keyBy('id');

        // Подгружаем названия локаций из старой базы
        $oldLocations = $this->getOldLocations($oldObjects);

        // Подгружаем количество фото из старой базы
        $oldPhotoCounts = DB::connection('factor_dump')
            ->table('object_images')
            ->whereIn('object_id', $oldIds)
            ->whereNull('deleted_at')
            ->selectRaw('object_id, COUNT(*) as cnt')
            ->groupBy('object_id')
            ->pluck('cnt', 'object_id');

        // Общая статистика
        $stats = [
            'total_new' => Property::count(),
            'total_old' => DB::connection('factor_dump')
                ->table('objects')
                ->whereIn('status', [1, 2, 3])
                ->where('rent', 0)
                ->where('deleted', 0)
                ->count(),
            'apartments' => Property::where('property_type_id', 23)->count(),
            'houses' => Property::where('property_type_id', 28)->count(),
            'commercial' => Property::where('property_type_id', 40)->count(),
            'photos_new' => PropertyPhoto::count(),
            'contacts' => DB::table('contactables')
                ->where('contactable_type', Property::class)
                ->count(),
            'translations' => \App\Models\Property\PropertyTranslation::count(),
            'with_complex' => Property::whereNotNull('complex_id')->count(),
        ];

        return view('pages.migration.verify', compact(
            'properties', 'oldObjects', 'oldLocations', 'oldPhotoCounts', 'stats',
            'search', 'typeFilter', 'perPage'
        ));
    }

    /**
     * AJAX: запуск одного теста для конкретного объекта.
     */
    public function verifyOne(Request $request, int $propertyId)
    {
        $property = Property::with(['city', 'district', 'zone', 'street', 'photos'])
            ->findOrFail($propertyId);

        $old = DB::connection('factor_dump')
            ->table('objects')
            ->where('id', $propertyId)
            ->first();

        if (!$old) {
            return response()->json([
                'status' => 'error',
                'message' => "Объект #{$propertyId} не найден в старой базе",
            ]);
        }

        $errors = $this->compareRecord($property, $old);

        return response()->json([
            'status' => empty($errors) ? 'ok' : 'mismatch',
            'property_id' => $propertyId,
            'errors' => $errors,
        ]);
    }

    /**
     * AJAX: запуск тестов для нескольких объектов (массово).
     */
    public function verifyBatch(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            // Если не указаны — проверяем все
            $ids = Property::orderBy('id')->pluck('id')->toArray();
        }

        $results = [];
        $okCount = 0;
        $failCount = 0;

        $properties = Property::with(['city', 'district', 'zone', 'street', 'photos'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $oldObjects = DB::connection('factor_dump')
            ->table('objects')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        foreach ($ids as $id) {
            $property = $properties[$id] ?? null;
            $old = $oldObjects[$id] ?? null;

            if (!$property || !$old) {
                $results[$id] = ['status' => 'error', 'errors' => ['Запись не найдена']];
                $failCount++;
                continue;
            }

            $errors = $this->compareRecord($property, $old);

            if (empty($errors)) {
                $okCount++;
                $results[$id] = ['status' => 'ok', 'errors' => []];
            } else {
                $failCount++;
                $results[$id] = ['status' => 'mismatch', 'errors' => $errors];
            }
        }

        return response()->json([
            'ok' => $okCount,
            'fail' => $failCount,
            'total' => count($ids),
            'results' => $results,
        ]);
    }

    /**
     * Сравнение одной записи: новый property vs старый object.
     * Возвращает массив ошибок (пустой = всё ОК).
     */
    protected function compareRecord(Property $property, object $old): array
    {
        $errors = [];

        // Числовые поля
        $numericFields = [
            'total_area' => ['area_total', 'Площадь общая'],
            'area_live' => ['area_living', 'Площадь жилая'],
            'area_kitchen' => ['area_kitchen', 'Площадь кухни'],
            // В старой базе поля перепутаны: all_floors = этаж, floor_build = этажность
            'all_floors' => ['floor', 'Этаж'],
            'floor_build' => ['floors_total', 'Этажность'],
            'price' => ['price', 'Цена'],
            'price_area' => ['price_per_m2', 'Цена за м²'],
        ];

        foreach ($numericFields as $oldField => [$newField, $label]) {
            $oldVal = (float) ($old->$oldField ?: 0);
            $newVal = (float) ($property->$newField ?: 0);
            if (abs($oldVal - $newVal) > 0.01) {
                $errors[] = "{$label}: старое={$oldVal}, новое={$newVal}";
            }
        }

        // Адрес: номер дома, квартира
        if (($old->number_house ?: null) !== $property->building_number) {
            $errors[] = "Номер дома: старое='{$old->number_house}', новое='{$property->building_number}'";
        }
        if (($old->num_flat ?: null) !== $property->apartment_number) {
            $errors[] = "Квартира: старое='{$old->num_flat}', новое='{$property->apartment_number}'";
        }

        // Локации (по названию)
        $this->compareLocation($errors, $old->town_id, $property->city, 'lib_towns', 'Город');
        $this->compareLocation($errors, $old->region_id, $property->district, 'lib_regions', 'Район');
        $this->compareLocation($errors, $old->zone_id, $property->zone, 'lib_zones', 'Жилмассив');
        $this->compareLocation($errors, $old->street_id, $property->street, 'lib_streets', 'Улица');

        // Координаты
        if (!empty($old->coords)) {
            $parts = explode(',', $old->coords);
            if (count($parts) === 2) {
                $oldLat = (float) trim($parts[0]);
                $oldLng = (float) trim($parts[1]);
                $newLat = (float) $property->latitude;
                $newLng = (float) $property->longitude;

                if (abs($oldLat - $newLat) > 0.0001) {
                    $errors[] = "Latitude: старое={$oldLat}, новое={$newLat}";
                }
                if (abs($oldLng - $newLng) > 0.0001) {
                    $errors[] = "Longitude: старое={$oldLng}, новое={$newLng}";
                }
            }
        }

        // Тип объекта
        $typeMap = [1 => 23, 2 => 28, 3 => 40];
        $expectedType = $typeMap[$old->status] ?? null;
        if ($expectedType && $property->property_type_id != $expectedType) {
            $errors[] = "Тип: ожидался={$expectedType}, получен={$property->property_type_id}";
        }

        // Количество фото
        $oldPhotoCount = DB::connection('factor_dump')
            ->table('object_images')
            ->where('object_id', $old->id)
            ->whereNull('deleted_at')
            ->count();
        $newPhotoCount = $property->photos->count();
        if ($oldPhotoCount !== $newPhotoCount) {
            $errors[] = "Фото: старое={$oldPhotoCount}, новое={$newPhotoCount}";
        }

        // --- Новые поля ---
        $data = json_decode($old->data ?? '{}', false);

        // ЖК
        if (!empty($old->complex) && $old->complex > 0) {
            if (!$property->complex_id) {
                $errors[] = "ЖК: в старой базе complex={$old->complex}, в новой complex_id=NULL";
            } else {
                $oldComplexName = DB::connection('factor_dump')
                    ->table('lib_other')->where('id', $old->complex)->value('name');
                $newComplexName = DB::table('complexes')
                    ->where('id', $property->complex_id)->value('name');
                if ($oldComplexName && $newComplexName
                    && mb_strtolower(trim($oldComplexName)) !== mb_strtolower(trim($newComplexName))) {
                    $errors[] = "ЖК: старое='{$oldComplexName}', новое='{$newComplexName}'";
                }
            }
        }

        // Год постройки
        $oldYear = !empty($data->year_building) ? (int) $data->year_building : null;
        if ($oldYear && $property->year_built != $oldYear) {
            $errors[] = "Год постройки: старое={$oldYear}, новое={$property->year_built}";
        }

        // Заголовок (translation)
        $translation = \App\Models\Property\PropertyTranslation::where('property_id', $property->id)
            ->where('locale', 'ru')
            ->first();

        $oldTitle = $data->title ?? null;
        if ($oldTitle) {
            if (!$translation || $translation->title !== $oldTitle) {
                $errors[] = "Заголовок: старое='" . mb_substr($oldTitle, 0, 50) . "', новое='" . mb_substr($translation->title ?? '', 0, 50) . "'";
            }
        }

        // Описание
        $oldDescription = $data->description ?? null;
        if ($oldDescription) {
            if (!$translation || $translation->description !== $oldDescription) {
                $errors[] = "Описание: не перенесено или не совпадает";
            }
        }

        // Контакт: привязка, имя, телефон, роль
        $oldPhone = $data->telephone ?? null;
        $oldName = $data->name_sale ?? null;
        if (!empty($oldPhone) || !empty($oldName)) {
            $contactable = DB::table('contactables')
                ->where('contactable_type', \App\Models\Property\Property::class)
                ->where('contactable_id', $property->id)
                ->first();

            if (!$contactable) {
                $errors[] = "Контакт: не привязан (тел: {$oldPhone}, имя: {$oldName})";
            } else {
                // Проверка имени
                $contact = DB::table('contacts')->where('id', $contactable->contact_id)->first();
                if ($contact && !empty($oldName)) {
                    if (mb_strtolower(trim($oldName)) !== mb_strtolower(trim($contact->first_name))) {
                        $errors[] = "Контакт имя: старое='{$oldName}', новое='{$contact->first_name}'";
                    }
                }

                // Проверка телефона
                if (!empty($oldPhone)) {
                    $normalizedPhone = preg_replace('/\D/', '', $oldPhone);
                    $hasPhone = DB::table('contact_phones')
                        ->where('contact_id', $contactable->contact_id)
                        ->where('phone', $normalizedPhone)
                        ->exists();
                    if (!$hasPhone) {
                        $errors[] = "Контакт телефон: {$normalizedPhone} не найден";
                    }
                }

                // Проверка роли
                $typeSaleRoles = [1 => 'Риелтор', 2 => 'Продавец'];
                $expectedRole = $typeSaleRoles[$old->type_sale ?? 0] ?? null;
                if ($expectedRole && $contactable->role !== $expectedRole) {
                    $errors[] = "Контакт роль: ожид='{$expectedRole}', факт='{$contactable->role}'";
                }
            }
        }

        // contact_type_id
        $contactTypeMap = [1 => 202, 2 => 195];
        $expectedContactType = $contactTypeMap[$old->type_sale ?? 0] ?? null;
        if ($expectedContactType && $property->contact_type_id != $expectedContactType) {
            $errors[] = "Тип контакта: ожид={$expectedContactType}, факт={$property->contact_type_id}";
        }

        // agent_notes
        $oldNotes = $data->notes ?? null;
        if ($oldNotes && $property->agent_notes !== $oldNotes) {
            $errors[] = "Заметки коллегам: не совпадает";
        }

        // Комиссия
        if (!empty($data->price_rieltor_proc)) {
            if ((float) $property->commission != (float) $data->price_rieltor_proc) {
                $errors[] = "Комиссия: старое={$data->price_rieltor_proc}%, новое={$property->commission}";
            }
            if ($property->commission_type !== 'percent') {
                $errors[] = "Тип комиссии: ожид='percent', факт='{$property->commission_type}'";
            }
        } elseif (!empty($data->price_rieltor)) {
            if ((float) $property->commission != (float) $data->price_rieltor) {
                $errors[] = "Комиссия: старое={$data->price_rieltor}, новое={$property->commission}";
            }
            if ($property->commission_type !== 'fixed') {
                $errors[] = "Тип комиссии: ожид='fixed', факт='{$property->commission_type}'";
            }
        }

        // YouTube
        $oldYoutube = $data->youtube ?? null;
        if (!empty($oldYoutube) && $property->youtube_url !== $oldYoutube) {
            $errors[] = "YouTube: не совпадает";
        }

        // external_url
        $expectedUrl = !empty($data->linkToAd) ? $data->linkToAd : ($old->rem_url ?: null);
        if (!empty($expectedUrl) && $property->external_url !== $expectedUrl) {
            $errors[] = "External URL: старое='" . mb_substr($expectedUrl, 0, 50) . "', новое='" . mb_substr($property->external_url ?? '', 0, 50) . "'";
        }

        // employee_id
        if ($property->user_id) {
            $expectedEmployee = \App\Models\Employee\Employee::where('user_id', $property->user_id)->value('id');
            if ($expectedEmployee && $property->employee_id != $expectedEmployee) {
                $errors[] = "Сотрудник: ожид employee_id={$expectedEmployee}, факт={$property->employee_id}";
            }
        }

        // is_visible_to_agents
        $expectedVisible = (bool) ($old->open ?? 1);
        if ((bool) $property->is_visible_to_agents !== $expectedVisible) {
            $errors[] = "Видимость: open={$old->open}, is_visible_to_agents={$property->is_visible_to_agents}";
        }

        // area_land
        $oldAreaLand = $old->area_total_ych_t ?: null;
        if (!$oldAreaLand && !empty($old->area_home)) {
            $parsed = (float) preg_replace('/[^\d.]/', '', $old->area_home);
            $oldAreaLand = $parsed > 0 ? $parsed : null;
        }
        if ($oldAreaLand && abs((float) $oldAreaLand - (float) ($property->area_land ?: 0)) > 0.01) {
            $errors[] = "Площадь участка: старое={$oldAreaLand}, новое={$property->area_land}";
        }

        // Timestamps
        if ($old->date_created) {
            $oldCreated = \Carbon\Carbon::parse($old->date_created)->format('Y-m-d H:i:s');
            $newCreated = $property->created_at->format('Y-m-d H:i:s');
            if ($oldCreated !== $newCreated) {
                $errors[] = "Дата создания: старое={$oldCreated}, новое={$newCreated}";
            }
        }

        // Features — информационная проверка (не ошибка, т.к. многие old features не имеют
        // точного совпадения по имени в словарях новой базы, это ожидаемо)

        return $errors;
    }

    /**
     * Сравнение одной локации по названию.
     */
    protected function compareLocation(array &$errors, ?int $oldId, $newModel, string $oldTable, string $label): void
    {
        if (!$oldId) return;

        $oldName = DB::connection('factor_dump')
            ->table($oldTable)
            ->where('id', $oldId)
            ->value('name');

        if (!$oldName) return;

        if (!$newModel) {
            $errors[] = "{$label}: старое='{$oldName}', новое=НЕ ПРИВЯЗАНО";
            return;
        }

        if (mb_strtolower(trim($oldName)) !== mb_strtolower(trim($newModel->name))) {
            $errors[] = "{$label}: старое='{$oldName}', новое='{$newModel->name}'";
        }
    }

    /**
     * Получить названия локаций из старой базы для набора объектов.
     */
    protected function getOldLocations($oldObjects): array
    {
        $locations = [];

        $townIds = $oldObjects->pluck('town_id')->filter()->unique()->toArray();
        $regionIds = $oldObjects->pluck('region_id')->filter()->unique()->toArray();
        $zoneIds = $oldObjects->pluck('zone_id')->filter()->unique()->toArray();
        $streetIds = $oldObjects->pluck('street_id')->filter()->unique()->toArray();

        $towns = !empty($townIds) ? DB::connection('factor_dump')
            ->table('lib_towns')->whereIn('id', $townIds)->pluck('name', 'id')->toArray() : [];
        $regions = !empty($regionIds) ? DB::connection('factor_dump')
            ->table('lib_regions')->whereIn('id', $regionIds)->pluck('name', 'id')->toArray() : [];
        $zones = !empty($zoneIds) ? DB::connection('factor_dump')
            ->table('lib_zones')->whereIn('id', $zoneIds)->pluck('name', 'id')->toArray() : [];
        $streets = !empty($streetIds) ? DB::connection('factor_dump')
            ->table('lib_streets')->whereIn('id', $streetIds)->pluck('name', 'id')->toArray() : [];

        foreach ($oldObjects as $obj) {
            $locations[$obj->id] = [
                'city' => $towns[$obj->town_id] ?? '—',
                'district' => $regions[$obj->region_id] ?? '—',
                'zone' => $zones[$obj->zone_id] ?? '—',
                'street' => $streets[$obj->street_id] ?? '—',
            ];
        }

        return $locations;
    }
}
