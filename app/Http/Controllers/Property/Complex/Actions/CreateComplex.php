<?php

namespace App\Http\Controllers\Property\Complex\Actions;

use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Бизнес-логика создания комплекса.
 *
 * Вся операция обёрнута в транзакцию:
 * создание записи, контакты, файлы (логотип/фото/планы), блоки.
 */
class CreateComplex
{
    /**
     * Создать комплекс со всеми связями.
     *
     * @param array $data Валидированные данные из StoreComplexRequest
     * @param Request $request Исходный запрос (для файлов)
     * @return Complex Созданный комплекс
     */
    public function execute(array $data, Request $request): Complex
    {
        return DB::transaction(function () use ($data, $request) {
            $mainName = $this->resolveMainName($data);
            $complex = $this->createComplex($data, $mainName);

            $this->attachContacts($complex, $data);
            $this->saveLogo($complex, $request);
            $this->savePhotos($complex, $request);
            $this->savePlans($complex, $request);
            $this->saveBlocks($complex, $data['blocks'] ?? [], $request);

            return $complex;
        });
    }

    /** Определить основное название (приоритет: RU -> UA -> EN) */
    private function resolveMainName(array $data, ?string $fallback = 'Без названия'): string
    {
        return $data['name_ru'] ?? $data['name_ua'] ?? $data['name_en'] ?? $fallback;
    }

    /** Создать запись комплекса в БД */
    private function createComplex(array $data, string $mainName): Complex
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

        return Complex::create([
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
            'is_active' => true,
            'source' => 'manual',
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

    /** Привязать контакты через полиморфную связь */
    private function attachContacts(Complex $complex, array $data): void
    {
        if (empty($data['contact_ids'])) {
            return;
        }

        $contactData = [];
        foreach ($data['contact_ids'] as $index => $id) {
            $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
        }
        $complex->contacts()->attach($contactData);
    }

    /** Сохранить логотип комплекса */
    private function saveLogo(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('logo')) {
            return;
        }

        $logoPath = $request->file('logo')->store("complexes/{$complex->id}/logo", 'public');
        $complex->update(['logo_path' => $logoPath]);
    }

    /** Сохранить фото комплекса в JSON */
    private function savePhotos(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        $photoPaths = [];
        foreach ($request->file('photos') as $photo) {
            $photoPaths[] = $photo->store("complexes/{$complex->id}/photos", 'public');
        }
        $complex->update(['photos' => $photoPaths]);
    }

    /** Сохранить планы комплекса в JSON */
    private function savePlans(Complex $complex, Request $request): void
    {
        if (!$request->hasFile('plans')) {
            return;
        }

        $planPaths = [];
        foreach ($request->file('plans') as $plan) {
            $planPaths[] = $plan->store("complexes/{$complex->id}/plans", 'public');
        }
        $complex->update(['plans' => $planPaths]);
    }

    /** Создать блоки комплекса с планами */
    private function saveBlocks(Complex $complex, array $blocksData, Request $request): void
    {
        foreach ($blocksData as $index => $blockData) {
            if (empty($blockData['name'])) {
                continue;
            }

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

            // Сохранить план блока
            $planKey = "blocks.{$index}.plan";
            if ($request->hasFile($planKey)) {
                $planPath = $request->file($planKey)->store("complexes/{$complex->id}/blocks/{$block->id}", 'public');
                $block->update(['plan_path' => $planPath]);
            }
        }
    }
}
