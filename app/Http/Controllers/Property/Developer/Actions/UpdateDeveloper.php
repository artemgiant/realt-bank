<?php

namespace App\Http\Controllers\Property\Developer\Actions;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Reference\Developer;
use App\Models\Reference\DeveloperLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Бизнес-логика обновления девелопера.
 *
 * Вся операция обёрнута в транзакцию:
 * обновление записи, синхронизация контактов, обновление логотипа, локации.
 */
class UpdateDeveloper
{
    /**
     * Обновить девелопера со всеми связями.
     *
     * @param Developer $developer Обновляемый девелопер
     * @param array $data Валидированные данные из UpdateDeveloperRequest
     * @param Request $request Исходный запрос (для файлов и локаций)
     * @return Developer Обновлённый девелопер
     */
    public function execute(Developer $developer, array $data, Request $request): Developer
    {
        return DB::transaction(function () use ($developer, $data, $request) {
            // Определяем основное название (приоритет: RU -> UA -> EN, fallback — текущее)
            $mainName = $data['name_ru'] ?? $data['name_ua'] ?? $data['name_en'] ?? $developer->name;

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

            // Обновляем девелопера
            $developer->update([
                'name' => $mainName,
                'slug' => Str::slug($mainName),
                'website' => $data['materials_url'] ?? null,
                'company_website' => $data['company_website'] ?? null,
                'description' => $data['description_ru'] ?? $data['description_ua'] ?? $data['description_en'] ?? null,
                'year_founded' => $data['year_founded'] ?? null,
                'agent_notes' => $data['agent_notes'] ?? null,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
            ]);

            // Синхронизируем контакты через полиморфную связь
            $this->syncContacts($developer, $data);

            // Обновляем логотип (удаляем старый при загрузке нового)
            $this->updateLogo($developer, $request);

            // Обновляем локации
            $this->saveLocations($developer, $request->input('locations', []));

            return $developer;
        });
    }

    /**
     * Синхронизировать контакты девелопера с ролями.
     * Первый контакт — primary, остальные — secondary.
     */
    private function syncContacts(Developer $developer, array $data): void
    {
        if (!isset($data['contact_ids'])) {
            return;
        }

        if (!empty($data['contact_ids'])) {
            $contactData = [];
            foreach ($data['contact_ids'] as $index => $id) {
                $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
            }
            $developer->contacts()->sync($contactData);
        } else {
            $developer->contacts()->detach();
        }
    }

    /**
     * Обновить логотип девелопера: удалить старый и сохранить новый.
     */
    private function updateLogo(Developer $developer, Request $request): void
    {
        if (!$request->hasFile('logo')) {
            return;
        }

        // Удаляем старый логотип
        if ($developer->logo_path) {
            Storage::disk('public')->delete($developer->logo_path);
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
