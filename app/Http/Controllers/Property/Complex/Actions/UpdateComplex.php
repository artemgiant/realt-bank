<?php

namespace App\Http\Controllers\Property\Complex\Actions;

use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Бизнес-логика обновления комплекса.
 *
 * Вся операция обёрнута в транзакцию:
 * обновление записи, контакты, медиа (логотип/фото/планы), блоки.
 */
class UpdateComplex
{
    /**
     * Обновить комплекс со всеми связями.
     *
     * @param Complex $complex Обновляемый комплекс
     * @param array $data Валидированные данные из UpdateComplexRequest
     * @param Request $request Исходный запрос (для файлов)
     * @return Complex Обновлённый комплекс
     */
    public function execute(Complex $complex, array $data, Request $request): Complex
    {
        return DB::transaction(function () use ($complex, $data, $request) {
            $mainName = $this->resolveMainName($data, $complex->name);

            $this->updateComplex($complex, $data, $mainName);
            $this->syncContacts($complex, $data);
            $this->updateLogo($complex, $request);
            $this->deleteMarkedPhotos($complex, $data);
            $this->deleteMarkedPlans($complex, $data);
            $this->addNewPhotos($complex, $request);
            $this->addNewPlans($complex, $request);
            $this->updateBlocks($complex, $data['blocks'] ?? [], $request);

            return $complex;
        });
    }

    /** Определить основное название (приоритет: RU -> UA -> EN -> текущее) */
    private function resolveMainName(array $data, string $fallback): string
    {
        return $data['name_ru'] ?? $data['name_ua'] ?? $data['name_en'] ?? $fallback;
    }

    /** Обновить запись комплекса в БД */
    private function updateComplex(Complex $complex, array $data, string $mainName): void
    {
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

        $complex->update([
            'name' => $mainName,
            'slug' => Str::slug($mainName),
            'developer_id' => $data['developer_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'zone_id' => $data['zone_id'] ?? null,
            'website' => $data['website'] ?? null,
            'company_website' => $data['company_website'] ?? null,
            'materials_url' => $data['materials_url'] ?? null,
            'description' => $data['description_ru'] ?? $data['description_ua'] ?? $data['description_en'] ?? null,
            'agent_notes' => $data['agent_notes'] ?? null,
            'special_conditions' => $data['special_conditions'] ?? null,
            'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
            'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
            'area_from' => $data['area_from'] ?? null,
            'area_to' => $data['area_to'] ?? null,
            'price_per_m2' => $data['price_per_m2'] ?? null,
            'price_total' => $data['price_total'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'objects_count' => $data['objects_count'] ?? null,
            'conditions' => $data['conditions'] ?? null,
            'features' => $data['features'] ?? null,
            'categories' => $data['categories'] ?? null,
            'object_types' => $data['object_types'] ?? null,
            'housing_classes' => $data['housing_classes'] ?? null,
        ]);
    }

    /** Синхронизировать контакты комплекса */
    private function syncContacts(Complex $complex, array $data): void
    {
        $complex->contacts()->detach();

        if (!empty($data['contact_ids'])) {
            $contactData = [];
            foreach ($data['contact_ids'] as $index => $id) {
                $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
            }
            $complex->contacts()->attach($contactData);
        }
    }

    /** Обновить логотип (удалить старый, загрузить новый) */
    private function updateLogo(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('logo')) {
            return;
        }

        if ($complex->logo_path) {
            Storage::disk('public')->delete($complex->logo_path);
        }

        $logoPath = $request->file('logo')->store("complexes/{$complex->id}/logo", 'public');
        $complex->update(['logo_path' => $logoPath]);
    }

    /** Удалить помеченные фото (по индексу в JSON массиве) */
    private function deleteMarkedPhotos(Complex $complex, array $data): void
    {
        if (empty($data['delete_photos'])) {
            return;
        }

        $existingPhotos = $complex->photos ?? [];
        foreach ($data['delete_photos'] as $index) {
            if (isset($existingPhotos[$index])) {
                Storage::disk('public')->delete($existingPhotos[$index]);
                unset($existingPhotos[$index]);
            }
        }
        $complex->update(['photos' => array_values($existingPhotos)]);
    }

    /** Удалить помеченные планы (по индексу в JSON массиве) */
    private function deleteMarkedPlans(Complex $complex, array $data): void
    {
        if (empty($data['delete_plans'])) {
            return;
        }

        $existingPlans = $complex->plans ?? [];
        foreach ($data['delete_plans'] as $index) {
            if (isset($existingPlans[$index])) {
                Storage::disk('public')->delete($existingPlans[$index]);
                unset($existingPlans[$index]);
            }
        }
        $complex->update(['plans' => array_values($existingPlans)]);
    }

    /** Добавить новые фото в JSON массив */
    private function addNewPhotos(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $existingPhotos = $complex->photos ?? [];
        foreach ($request->file('photos') as $photo) {
            $existingPhotos[] = $photo->store("complexes/{$complex->id}/photos", 'public');
        }
        $complex->update(['photos' => $existingPhotos]);
    }

    /** Добавить новые планы в JSON массив */
    private function addNewPlans(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('plans')) {
            return;
        }

        $existingPlans = $complex->plans ?? [];
        foreach ($request->file('plans') as $plan) {
            $existingPlans[] = $plan->store("complexes/{$complex->id}/plans", 'public');
        }
        $complex->update(['plans' => $existingPlans]);
    }

    /** Обновить блоки комплекса (создание, обновление, удаление) */
    private function updateBlocks(Complex $complex, array $blocksData, Request $request): void
    {
        foreach ($blocksData as $index => $blockData) {
            // Удалить блок, помеченный на удаление
            if (!empty($blockData['delete'])) {
                if (!empty($blockData['id'])) {
                    $block = Block::find($blockData['id']);
                    if ($block) {
                        if ($block->plan_path) {
                            Storage::disk('public')->delete($block->plan_path);
                        }
                        $block->delete();
                    }
                }
                continue;
            }

            if (empty($blockData['name'])) {
                continue;
            }

            if (!empty($blockData['id'])) {
                // Обновить существующий блок
                $block = Block::find($blockData['id']);
                if ($block) {
                    $block->update([
                        'name' => $blockData['name'],
                        'slug' => Str::slug($blockData['name']),
                        'street_id' => $blockData['street_id'] ?? null,
                        'building_number' => $blockData['building_number'] ?? null,
                        'floors_total' => $blockData['floors_total'] ?? null,
                        'year_built' => $blockData['year_built'] ?? null,
                        'heating_type_id' => $blockData['heating_type_id'] ?? null,
                        'wall_type_id' => $blockData['wall_type_id'] ?? null,
                    ]);
                }
            } else {
                // Создать новый блок
                $block = Block::create([
                    'name' => $blockData['name'],
                    'slug' => Str::slug($blockData['name']),
                    'complex_id' => $complex->id,
                    'developer_id' => $complex->developer_id,
                    'street_id' => $blockData['street_id'] ?? null,
                    'building_number' => $blockData['building_number'] ?? null,
                    'floors_total' => $blockData['floors_total'] ?? null,
                    'year_built' => $blockData['year_built'] ?? null,
                    'heating_type_id' => $blockData['heating_type_id'] ?? null,
                    'wall_type_id' => $blockData['wall_type_id'] ?? null,
                    'is_active' => true,
                    'source' => 'manual',
                ]);
            }

            // Обновить план блока
            $planKey = "blocks.{$index}.plan";
            if ($request->hasFile($planKey)) {
                if ($block->plan_path) {
                    Storage::disk('public')->delete($block->plan_path);
                }
                $planPath = $request->file($planKey)->store("complexes/{$complex->id}/blocks/{$block->id}", 'public');
                $block->update(['plan_path' => $planPath]);
            }
        }
    }
}
