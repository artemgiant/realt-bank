<?php

namespace App\Http\Controllers\Company\Company\Actions;

use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Бизнес-логика создания компании.
 *
 * Вся операция обёрнута в транзакцию:
 * создание записи, переводы, контакты, логотип, офисы.
 */
class CreateCompany
{
    /**
     * Создать компанию со всеми связями.
     *
     * @param array $data Валидированные данные из StoreCompanyRequest
     * @param Request $request Исходный запрос (для файла логотипа)
     * @return Company Созданная компания
     */
    public function execute(array $data, Request $request): Company
    {
        return DB::transaction(function () use ($data, $request) {
            // Определяем основное название (приоритет: RU -> UA -> EN)
            $mainName = $data['name_ru']
                ?? $data['name_ua']
                ?? $data['name_en']
                ?? 'Без названия';

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

            // Создаём компанию
            $company = Company::create([
                'name' => $mainName,
                'slug' => Str::slug($mainName) . '-' . uniqid(),
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'description_translations' => !empty($descriptionTranslations) ? $descriptionTranslations : null,
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'district_id' => $data['district_id'] ?? null,
                'zone_id' => $data['zone_id'] ?? null,
                'street_id' => $data['street_id'] ?? null,
                'building_number' => $data['building_number'] ?? null,
                'office_number' => $data['office_number'] ?? null,
                'website' => $data['website'] ?? null,
                'edrpou_code' => $data['edrpou_code'] ?? null,
                'company_type' => $data['company_type'] ?? null,
                'is_active' => true,
            ]);

            $this->attachContacts($company, $data);
            $this->saveLogo($company, $request);
            $this->saveOffices($company, $data['offices'] ?? []);

            return $company;
        });
    }

    /**
     * Привязать контакты через полиморфную связь с ролями.
     */
    private function attachContacts(Company $company, array $data): void
    {
        if (!empty($data['contact_ids'])) {
            $contactData = [];
            foreach ($data['contact_ids'] as $index => $id) {
                $contactData[$id] = ['role' => ($index === 0 ? 'primary' : 'secondary')];
            }
            $company->contacts()->attach($contactData);
        }
    }

    /**
     * Сохранить логотип компании в storage.
     */
    private function saveLogo(Company $company, Request $request): void
    {
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store("companies/{$company->id}/logo", 'public');
            $company->update(['logo_path' => $logoPath]);
        }
    }

    /**
     * Сохранить офисы компании.
     *
     * Удаляет старые офисы и создаёт новые с контактами.
     * Для каждого офиса определяет основное название (приоритет: RU -> UA -> EN),
     * кеширует полный адрес и привязывает контакты.
     */
    private function saveOffices(Company $company, array $offices): void
    {
        // Удаляем старые офисы
        foreach ($company->offices as $office) {
            $office->contacts()->detach();
            $office->delete();
        }

        // Добавляем новые офисы
        foreach ($offices as $index => $officeData) {
            // Проверяем, есть ли хотя бы одно название
            $nameUa = $officeData['name_ua'] ?? null;
            $nameRu = $officeData['name_ru'] ?? null;
            $nameEn = $officeData['name_en'] ?? null;

            if (empty($nameUa) && empty($nameRu) && empty($nameEn)) {
                continue;
            }

            // Основное название (приоритет: RU -> UA -> EN)
            $mainName = $nameRu ?? $nameUa ?? $nameEn ?? 'Офис ' . ($index + 1);

            // Мультиязычные названия
            $nameTranslations = array_filter([
                'ua' => $nameUa,
                'ru' => $nameRu,
                'en' => $nameEn,
            ]);

            $office = CompanyOffice::create([
                'company_id' => $company->id,
                'name' => $mainName,
                'name_translations' => !empty($nameTranslations) ? $nameTranslations : null,
                'country_id' => $officeData['country_id'] ?? null,
                'state_id' => $officeData['state_id'] ?? null,
                'city_id' => $officeData['city_id'] ?? null,
                'district_id' => $officeData['district_id'] ?? null,
                'zone_id' => $officeData['zone_id'] ?? null,
                'street_id' => $officeData['street_id'] ?? null,
                'building_number' => $officeData['building_number'] ?? null,
                'office_number' => $officeData['office_number'] ?? null,
                'sort_order' => $index,
                'is_active' => true,
            ]);

            // Кешируем полный адрес
            $office->load(['street', 'city']);
            $office->update(['full_address' => $office->full_address_computed]);

            // Привязываем контакты офиса
            if (!empty($officeData['contact_ids'])) {
                $contactData = [];
                foreach ($officeData['contact_ids'] as $idx => $id) {
                    $contactData[$id] = ['role' => ($idx === 0 ? 'primary' : 'secondary')];
                }
                $office->contacts()->attach($contactData);
            }
        }
    }
}
