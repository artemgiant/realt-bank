<?php

namespace App\Http\Controllers\Property\Developer\Actions;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Reference\Developer;
use App\Models\Reference\DeveloperLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Бизнес-логика создания девелопера.
 *
 * Вся операция обёрнута в транзакцию:
 * определение названия, переводы, создание записи, привязка контактов, логотип, локации.
 */
class CreateDeveloper
{
    /**
     * Создать девелопера со всеми связями.
     *
     * @param array $data Валидированные данные из StoreDeveloperRequest
     * @param Request $request Исходный запрос (для файлов и локаций)
     * @return Developer Созданный девелопер
     */
    public function execute(array $data, Request $request): Developer
    {
        return DB::transaction(function () use ($data, $request) {
            // Определяем основное название (приоритет: RU -> UA -> EN)
            $mainName = $data['name_ru'] ?? $data['name_ua'] ?? $data['name_en'] ?? 'Без названия';

            // Подготавливаем мультиязычные данные
            $nameTranslations = array_filter([
                'ua' => $data['name_ua'] ?? null,
                'ru' => $data['name_ru'] ?? null,
                'en' => $data['name_en'] ?? null,
            ]);

            $descriptionTranslations = array_filter([
                'ua' => $data['description_ua'] ?? null,
                'ru' => $data['description_ru'] ?? null,
                'en' => $data['description_en'] ?? null,
            ]);

            // Создаём девелопера
            $developer = Developer::create([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'website' => $data['materials_url'] ?? null,
                'company_website' => $data['company_website'] ?? null,
                'description' => $data['description_ru'] ?? $data['description_ua'] ?? $data['description_en'] ?? null,
                'year_founded' => $data['year_founded'] ?? null,
                'agent_notes' => $data['agent_notes'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'is_active' => true,
            ]);

            // Привязываем контакты через полиморфную связь
            $this->attachContacts($developer, $data);

            // Сохраняем логотип
            $this->saveLogo($developer, $request);

            // Сохраняем локации
            $this->saveLocations($developer, $request->input('locations', []));

            return $developer;
        });
    }

    /**
     * Привязать контакты к девелоперу с ролями (первый — primary, остальные — secondary).
     */
    private function attachContacts(Developer $developer, array $data): void
    {
        if (empty($data['contact_ids'])) {
            return;
        }

        $contactData = [];
        foreach ($data['contact_ids'] as $index => $id) {
            $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
        }
        $developer->contacts()->attach($contactData);
    }

    /**
     * Сохранить логотип девелопера в storage.
     */
    private function saveLogo(Developer $developer, Request $request): void
    {
        if (!$request->hasFile('logo')) {
            return;
        }

        $logoPath = $request->file('logo')->store("developers/{$developer->id}/logo", 'public');
        $developer->update(['logo_path' => $logoPath]);
    }

    /**
     * Сохранение локаций девелопера.
     * Удаляет старые и добавляет новые из массива формата "type:id".
     */
    private function saveLocations(Developer $developer, array $locations): void
    {
        // Удаляем старые локации
        $developer->locations()->delete();

        // Добавляем новые
        foreach ($locations as $locationData) {
            $locationValue = $locationData['location'] ?? null;

            if (empty($locationValue)) {
                continue;
            }

            // Парсим значение формата "type:id" (например "city:123")
            $parts = explode(':', $locationValue);
            if (count($parts) !== 2) {
                continue;
            }

            $locationType = $parts[0];
            $locationId = (int) $parts[1];

            // Валидация типа
            if (!in_array($locationType, ['country', 'state', 'city'])) {
                continue;
            }

            // Получаем название локации
            $locationName = '';
            $fullLocationName = '';

            switch ($locationType) {
                case 'country':
                    $location = Country::find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                    }
                    break;

                case 'state':
                    $location = State::with('country')->find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                        if ($location->country) {
                            $fullLocationName = $location->country->name . ', ' . $location->name;
                        }
                    }
                    break;

                case 'city':
                    $location = City::with(['state.country'])->find($locationId);
                    if ($location) {
                        $locationName = $location->name;
                        $fullLocationName = $location->name;
                        if ($location->state) {
                            $fullLocationName = $location->state->name . ', ' . $location->name;
                            if ($location->state->country) {
                                $fullLocationName = $location->state->country->name . ', ' . $fullLocationName;
                            }
                        }
                    }
                    break;
            }

            if (!empty($locationName)) {
                DeveloperLocation::create([
                    'developer_id' => $developer->id,
                    'location_type' => $locationType,
                    'location_id' => $locationId,
                    'location_name' => $locationName,
                    'full_location_name' => $fullLocationName,
                ]);
            }
        }
    }
}
