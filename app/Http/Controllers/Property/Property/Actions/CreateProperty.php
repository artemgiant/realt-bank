<?php

namespace App\Http\Controllers\Property\Property\Actions;

use App\Models\Employee\Employee;
use App\Models\Property\Property;
use App\Models\Property\PropertyDocument;
use App\Models\Property\PropertyTranslation;
use App\Services\PhotoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Бизнес-логика создания объекта недвижимости.
 *
 * Вся операция обёрнута в транзакцию:
 * создание записи, переводы, контакты, особенности, документы, фотографии.
 */
class CreateProperty
{
    /**
     * Создать объект недвижимости со всеми связями.
     *
     * @param array $data Валидированные данные из StorePropertyRequest
     * @param Request $request Исходный запрос (для файлов и features)
     * @return Property Созданный объект
     */
    public function execute(array $data, Request $request): Property
    {
        return DB::transaction(function () use ($data, $request) {
            $pricePerM2 = $this->calculatePricePerM2($data);

            [$userId, $employeeId] = $this->resolveAgent($data);

            $property = Property::create([
                'user_id' => $userId,
                'employee_id' => $employeeId,

                // Required
                'deal_type_id' => $data['deal_type_id'],
                'currency_id' => $data['currency_id'],

                // Dictionaries
                'deal_kind_id' => $data['deal_kind_id'] ?? null,
                'building_type_id' => $data['building_type_id'] ?? null,
                'property_type_id' => $data['property_type_id'] ?? null,
                'room_count_id' => $data['room_count_id'] ?? null,
                'condition_id' => $data['condition_id'] ?? null,
                'bathroom_count_id' => $data['bathroom_count_id'] ?? null,
                'ceiling_height_id' => $data['ceiling_height_id'] ?? null,
                'wall_type_id' => $data['wall_type_id'] ?? null,
                'heating_type_id' => $data['heating_type_id'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'contact_type_id' => $data['contact_type_id'] ?? null,

                // Location
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'region_id' => $data['region_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'district_id' => $data['district_id'] ?? null,
                'zone_id' => $data['zone_id'] ?? null,
                'street_id' => $data['street_id'] ?? null,
                'building_number' => $data['building_number'] ?? null,
                'apartment_number' => $data['apartment_number'] ?? null,

                // Complex
                'complex_id' => $data['complex_id'] ?? null,
                'block_id' => $data['block_id'] ?? null,

                // Numbers
                'area_total' => $data['area_total'] ?? null,
                'area_living' => $data['area_living'] ?? null,
                'area_kitchen' => $data['area_kitchen'] ?? null,
                'area_land' => $data['area_land'] ?? null,
                'floor' => $data['floor'] ?? null,
                'floors_total' => $data['floors_total'] ?? null,
                'year_built' => $data['year_built'] ?? null,
                'price' => $data['price'] ?? null,
                'price_per_m2' => $pricePerM2,
                'commission' => $data['commission'] ?? null,
                'commission_type' => 'percent',

                // Text
                'youtube_url' => $data['youtube_url'] ?? null,
                'personal_notes' => $data['personal_notes'] ?? null,
                'agent_notes' => $data['agent_notes'] ?? null,

                'is_advertised' => !empty($data['is_advertised']),
                'is_visible_to_agents' => !empty($data['is_visible_to_agents']),

                // Defaults
                'status' => 'draft',
            ]);

            $this->saveTranslations($property, $data);
            $this->attachContacts($property, $data);
            $this->syncFeatures($property, $request);
            $this->saveDocuments($property, $request);
            $this->savePhotos($property, $request);

            return $property;
        });
    }

    /** Вычислить цену за м² (price / area_total) */
    private function calculatePricePerM2(array $data): ?float
    {
        if (!empty($data['price']) && !empty($data['area_total']) && $data['area_total'] > 0) {
            return $data['price'] / $data['area_total'];
        }
        return null;
    }

    /**
     * Определить агента (сотрудника) для объекта.
     * Если передан assigned_agent_id — назначаем его.
     * Иначе — текущий пользователь как агент.
     * @return array [user_id, employee_id]
     */
    private function resolveAgent(array $data): array
    {
        $userId = auth()->id();
        $employeeId = null;

        if (!empty($data['assigned_agent_id'])) {
            $assignedAgent = Employee::find($data['assigned_agent_id']);
            if ($assignedAgent) {
                $employeeId = $assignedAgent->id;
                if ($assignedAgent->user_id) {
                    $userId = $assignedAgent->user_id;
                }
            }
        }

        if (!$employeeId) {
            $currentEmployee = Employee::where('user_id', auth()->id())->first();
            if ($currentEmployee) {
                $employeeId = $currentEmployee->id;
            }
        }

        return [$userId, $employeeId];
    }

    /** Сохранить переводы (заголовок и описание) для ua, ru, en */
    private function saveTranslations(Property $property, array $data): void
    {
        $locales = ['ua', 'ru', 'en'];

        foreach ($locales as $locale) {
            $title = $data['title_ru'] ?? null;
            $description = $data["description_{$locale}"] ?? null;

            if ($description) {
                $description = $property->id . ' ' . $description;
            }

            if ($title || $description) {
                PropertyTranslation::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $title ?? '',
                        'description' => $description,
                    ]
                );
            }
        }
    }

    /** Привязать контакты (клиентов) к объекту */
    private function attachContacts(Property $property, array $data): void
    {
        if (!empty($data['contact_ids'])) {
            $property->contacts()->attach($data['contact_ids']);
        }
    }

    /** Синхронизировать особенности объекта (балкон, парковка и т.д.) */
    private function syncFeatures(Property $property, Request $request): void
    {
        if ($request->has('features')) {
            $property->features()->sync($request->input('features', []));
        }
    }

    /** Сохранить загруженные документы в storage */
    private function saveDocuments(Property $property, Request $request): void
    {
        if (!$request->hasFile('documents')) {
            return;
        }

        foreach ($request->file('documents') as $file) {
            $filename = $file->getClientOriginalName();
            $path = $file->store("/properties/{$property->id}/documents");

            $property->documents()->create([
                'name' => pathinfo($filename, PATHINFO_FILENAME),
                'filename' => $filename,
                'path' => $path,
            ]);
        }
    }

    /** Загрузить фотографии через PhotoUploadService */
    private function savePhotos(Property $property, Request $request): void
    {
        if ($request->hasFile('photos')) {
            $photoService = app(PhotoUploadService::class);
            $photoService->uploadPhotos($property, $request->file('photos'));
        }
    }
}
