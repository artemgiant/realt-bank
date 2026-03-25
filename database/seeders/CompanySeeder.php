<?php

namespace Database\Seeders;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Reference\Company;
use App\Models\Reference\CompanyOffice;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Компания FAKTOR
        $company = Company::updateOrCreate(
            ['slug' => 'faktor'],
            [
                'name' => 'FAKTOR',
                'slug' => 'faktor',
                'name_translations' => [
                    'ru' => 'FAKTOR',
                    'uk' => 'FAKTOR',
                    'en' => 'FAKTOR',
                ],
                'company_type' => 'agency',
                'building_number' => '3Б',
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
        CompanyOffice::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'FAKTOR Manhattan'],
            [
                'company_id' => $company->id,
                'name' => 'FAKTOR Manhattan',
                'name_translations' => [
                    'ru' => 'FAKTOR Manhattan',
                    'uk' => 'FAKTOR Manhattan',
                    'en' => 'FAKTOR Manhattan',
                ],
                'phone' => '0733550057',
                'building_number' => '2/2',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
