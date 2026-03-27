<?php

namespace Database\Seeders;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Employee\Employee;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Если компания с slug 'faktor' уже существует — не создаём
        if (Company::where('slug', 'faktor')->exists()) {
            return;
        }

        // Компания FAKTOR
        $company = Company::updateOrCreate(
            ['slug' => 'faktor'],
            [
                'name' => 'FAKTOR',
                'slug' => 'faktor',
                'name_translations' => [
                    'en' => 'FAKTOR',
                    'ru' => 'FAKTOR',
                    'ua' => 'FAKTOR',
                ],
                'country_id' => 1,
                'state_id' => 14,
                'region_id' => 27,
                'city_id' => 1,
                'district_id' => 1,
                'zone_id' => 2,
                'street_id' => 45,
                'building_number' => '3Б',
                'website' => 'https://faktor24.com',
                'edrpou_code' => '76578456485',
                'company_type' => 264,
                'is_active' => true,
            ]
        );

        // Контакт: Гуцу Вадим — Керівник
        $contact = Contact::updateOrCreate(
            ['first_name' => 'Вадим', 'last_name' => 'Гуцу'],
            [
                'first_name' => 'Вадим',
                'last_name' => 'Гуцу',
                'company_id' => $company->id,
            ]
        );

        // Телефон контакта
        ContactPhone::updateOrCreate(
            ['contact_id' => $contact->id, 'phone' => '0733559854'],
            [
                'contact_id' => $contact->id,
                'phone' => '0733559854',
                'is_primary' => true,
            ]
        );

        // Привязка контакта к компании с ролью «Керівник» (через pivot, без добавления в справочник ролей)
        $company->contacts()->syncWithoutDetaching([
            $contact->id => ['role' => 'Керівник'],
        ]);

        // Офис: FAKTOR Manhattan
        $office = CompanyOffice::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'FAKTOR Manhattan'],
            [
                'company_id' => $company->id,
                'name' => 'FAKTOR Manhattan',
                'name_translations' => [
                    'ru' => 'FAKTOR Manhattan',
                ],
                'country_id' => 1,
                'state_id' => 14,
                'region_id' => 27,
                'city_id' => 1,
                'district_id' => 2,
                'zone_id' => 3,
                'street_id' => 166,
                'building_number' => '2/2',
                'phone' => '(73) 355-00-57',
                'full_address' => 'Филатова Академика, 2/2, Одесса',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        // Привязка всех сотрудников к компании и офису
        Employee::whereNull('office_id')
            ->orWhereNull('company_id')
            ->update([
                'company_id' => $company->id,
                'office_id' => $office->id,
            ]);
    }
}
