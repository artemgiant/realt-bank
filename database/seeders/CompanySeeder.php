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
        // Компания FAKTOR (id=1)
        $company = Company::updateOrCreate(
            ['id' => 1],
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

        ContactPhone::updateOrCreate(
            ['contact_id' => $contact->id, 'phone' => '0733559854'],
            [
                'contact_id' => $contact->id,
                'phone' => '0733559854',
                'is_primary' => true,
            ]
        );

        $company->contacts()->syncWithoutDetaching([
            $contact->id => ['role' => 'Керівник'],
        ]);

        // Офисы
        $offices = [
            ['id' => 5, 'name' => 'FAKTOR Manhattan', 'name_translations' => ['en' => 'FAKTOR Manhattan', 'ru' => 'FAKTOR Manhattan', 'ua' => 'FAKTOR Manhattan'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 2, 'zone_id' => 3, 'street_id' => 166, 'building_number' => '2/2', 'phone' => '(73) 355-00-57', 'full_address' => 'Филатова Академика, 2/2, Одесса', 'sort_order' => 0],
            ['id' => 6, 'name' => 'FAKTOR Kanatna', 'name_translations' => ['en' => 'FAKTOR Kanatna', 'ru' => 'FAKTOR Kanatna', 'ua' => 'FAKTOR Kanatna'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 24, 'building_number' => '79', 'phone' => '(50) 667-89-45', 'full_address' => 'Канатная, 79, Одесса', 'sort_order' => 1],
            ['id' => 7, 'name' => 'FAKTOR Apart Royal', 'name_translations' => ['en' => 'FAKTOR Apart Royal', 'ru' => 'FAKTOR Apart Royal', 'ua' => 'FAKTOR Apart Royal'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 44, 'building_number' => '71', 'phone' => '(73) 355-64-98', 'full_address' => 'Малая Арнаутская, 71, Одесса', 'sort_order' => 2],
            ['id' => 8, 'name' => 'FAKTOR Arcadia 7', 'name_translations' => ['en' => 'FAKTOR Arcadia 7', 'ru' => 'FAKTOR Arcadia 7', 'ua' => 'FAKTOR Arcadia 7'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 2, 'street_id' => 45, 'building_number' => '3Б', 'phone' => '(96) 634-69-64', 'full_address' => 'Генуэзская, 3Б, Одесса', 'sort_order' => 3],
            ['id' => 9, 'name' => 'FAKTOR Soborna', 'name_translations' => ['en' => 'FAKTOR Soborna', 'ru' => 'FAKTOR Soborna', 'ua' => 'FAKTOR Soborna'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 1191, 'building_number' => '3', 'phone' => '(73) 525-15-15', 'full_address' => 'Веры Холодной площадь, 3, Одесса', 'sort_order' => 4],
            ['id' => 10, 'name' => 'FAKTOR Arcadia 9', 'name_translations' => ['en' => 'FAKTOR Arcadia 9', 'ru' => 'FAKTOR Arcadia 9', 'ua' => 'FAKTOR Arcadia 9'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 2, 'street_id' => 45, 'building_number' => '3Б', 'phone' => '(73) 355-85-76', 'full_address' => 'Генуэзская, 3Б, Одесса', 'sort_order' => 5],
            ['id' => 11, 'name' => 'FAKTOR Manhattan', 'name_translations' => ['en' => 'FAKTOR Manhattan', 'ru' => 'FAKTOR Manhattan', 'ua' => 'FAKTOR Manhattan'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 2, 'zone_id' => 3, 'street_id' => 166, 'building_number' => '2/2', 'phone' => '(73) 355-34-84', 'full_address' => 'Филатова Академика, 2/2, Одесса', 'sort_order' => 0],
            ['id' => 20, 'name' => 'FAKTOR Kanatna', 'name_translations' => ['en' => 'FAKTOR Kanatna', 'ru' => 'FAKTOR Kanatna', 'ua' => 'FAKTOR Kanatna'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 24, 'building_number' => '79', 'phone' => '(50) 667-89-45', 'full_address' => 'Канатная, 79, Одесса', 'sort_order' => 1],
            ['id' => 21, 'name' => 'FAKTOR Apart Royal', 'name_translations' => ['en' => 'FAKTOR Apart Royal', 'ru' => 'FAKTOR Apart Royal', 'ua' => 'FAKTOR Apart Royal'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 44, 'building_number' => '71', 'phone' => '(73) 355-64-98', 'full_address' => 'Малая Арнаутская, 71, Одесса', 'sort_order' => 2],
            ['id' => 23, 'name' => 'FAKTOR Soborna', 'name_translations' => ['en' => 'FAKTOR Soborna', 'ru' => 'FAKTOR Soborna', 'ua' => 'FAKTOR Soborna'], 'country_id' => 1, 'state_id' => 14, 'region_id' => 27, 'city_id' => 1, 'district_id' => 1, 'zone_id' => 1, 'street_id' => 1191, 'building_number' => '3', 'phone' => '(73) 525-15-15', 'full_address' => 'Веры Холодной площадь, 3, Одесса', 'sort_order' => 4],
        ];

        foreach ($offices as $officeData) {
            $id = $officeData['id'];
            unset($officeData['id']);

            CompanyOffice::updateOrCreate(
                ['id' => $id],
                array_merge($officeData, [
                    'company_id' => $company->id,
                    'is_active' => true,
                ])
            );
        }

        // Сотрудники
        $employees = [
            ['id' => 1, 'user_id' => 1, 'office_id' => 5, 'position_id' => null, 'status_id' => null, 'first_name' => 'Админ', 'last_name' => 'Головенко', 'middle_name' => null, 'email' => null, 'phone' => '+38 (095) 090-22-93'],
            ['id' => 2, 'user_id' => 2, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Юлия', 'last_name' => 'Солодова', 'middle_name' => 'Викторовна', 'email' => 'user_28@factor.local', 'phone' => '+38 (095) 493-36-02'],
            ['id' => 3, 'user_id' => 3, 'office_id' => 10, 'position_id' => 332, 'status_id' => 333, 'first_name' => 'Вадим', 'last_name' => 'Гуцу', 'middle_name' => 'Федорович', 'email' => 'user_33@factor.local', 'phone' => '+38 (063) 655-77-88'],
            ['id' => 4, 'user_id' => 4, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Юлия', 'last_name' => 'Курова', 'middle_name' => 'Александровна', 'email' => 'user_34@factor.local', 'phone' => '+38 (067) 123-17-60'],
            ['id' => 5, 'user_id' => 5, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Юлия', 'last_name' => 'Шевченко ИЦ', 'middle_name' => 'Викторовна', 'email' => 'user_35@factor.local', 'phone' => '+38 (073) 355-00-57'],
            ['id' => 6, 'user_id' => 6, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Людмила', 'last_name' => 'Голубева', 'middle_name' => 'Юрьевна', 'email' => 'L0954162992@gmail.com', 'phone' => '+38 (095) 214-23-36'],
            ['id' => 7, 'user_id' => 7, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталья', 'last_name' => 'Мосягина', 'middle_name' => 'Евгеньевна', 'email' => '0504720452@parolREM', 'phone' => '+38 (097) 453-96-26'],
            ['id' => 8, 'user_id' => 8, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Светлана', 'last_name' => 'Гетьман', 'middle_name' => 'Алексеевна', 'email' => 'getman-sletlana@email.ua', 'phone' => '+38 (067) 987-45-52'],
            ['id' => 9, 'user_id' => 9, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лидия', 'last_name' => 'Хамщук', 'middle_name' => 'Федоровна', 'email' => 'user_75@factor.local', 'phone' => '+38 (067) 733-81-63'],
            ['id' => 10, 'user_id' => 10, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анна', 'last_name' => 'Отдел Продаж', 'middle_name' => null, 'email' => 'user_152@factor.local', 'phone' => '+38 (073) 000-00-00'],
            ['id' => 11, 'user_id' => 11, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Юлия', 'last_name' => 'Зайцева', 'middle_name' => 'Викторовна', 'email' => 'user_185@factor.local', 'phone' => '+38 (067) 654-69-05'],
            ['id' => 12, 'user_id' => 12, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Дмитро', 'last_name' => 'Божков', 'middle_name' => 'Олександрович', 'email' => 'user_186@factor.local', 'phone' => '+38 (093) 871-17-58'],
            ['id' => 13, 'user_id' => 13, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Инга', 'last_name' => 'Сергеева', 'middle_name' => 'Александровна', 'email' => 'user_202@factor.local', 'phone' => '+38 (067) 760-59-33'],
            ['id' => 14, 'user_id' => 14, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Евгения', 'last_name' => 'Фактор F3', 'middle_name' => 'Васильевна', 'email' => 'user_205@factor.local', 'phone' => '+38 (050) 667-89-45'],
            ['id' => 15, 'user_id' => 15, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Екатерина', 'last_name' => 'Згырчибаба', 'middle_name' => 'Александровна', 'email' => 'katerinazchibaba@gmail.com', 'phone' => '+38 (067) 119-18-55'],
            ['id' => 16, 'user_id' => 16, 'office_id' => 6, 'position_id' => 329, 'status_id' => 333, 'first_name' => 'Жанна', 'last_name' => 'Хименко', 'middle_name' => 'Васильевна', 'email' => 'user_212@factor.local', 'phone' => '+38 (067) 973-10-11'],
            ['id' => 17, 'user_id' => 17, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анна', 'last_name' => 'Фактор 1', 'middle_name' => null, 'email' => 'user_214@factor.local', 'phone' => '+38 (010) 000-00-00'],
            ['id' => 18, 'user_id' => 18, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ольга', 'last_name' => 'Шевченко', 'middle_name' => 'Александровна', 'email' => 'olya.krekova@gmail.com', 'phone' => '+38 (095) 137-93-00'],
            ['id' => 19, 'user_id' => 19, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Марина', 'last_name' => 'Зверева', 'middle_name' => 'Павловна', 'email' => 'user_247@factor.local', 'phone' => '+38 (096) 002-06-83'],
            ['id' => 20, 'user_id' => 20, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Сергей', 'last_name' => 'Гуцу', 'middle_name' => 'Федорович', 'email' => 'user_254@factor.local', 'phone' => '+38 (099) 523-58-50'],
            ['id' => 21, 'user_id' => 21, 'office_id' => 5, 'position_id' => 329, 'status_id' => 333, 'first_name' => 'Анна', 'last_name' => 'Макогон', 'middle_name' => 'Александровна', 'email' => 'user_255@factor.local', 'phone' => '+38 (066) 635-19-02'],
            ['id' => 22, 'user_id' => 22, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Марина', 'last_name' => 'Ботнарь', 'middle_name' => 'Константиновна', 'email' => 'user_263@factor.local', 'phone' => '+38 (093) 906-44-39'],
            ['id' => 23, 'user_id' => 23, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ольга', 'last_name' => 'Фактор 4', 'middle_name' => null, 'email' => 'user_275@factor.local', 'phone' => '+38 (020) 000-00-00'],
            ['id' => 24, 'user_id' => 24, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Чудина', 'middle_name' => 'Витальевна', 'email' => 'user_282@factor.local', 'phone' => '+38 (097) 318-77-66'],
            ['id' => 25, 'user_id' => 25, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Яна', 'last_name' => 'Сидорова', 'middle_name' => 'Дмитриевна', 'email' => 'vbelyaeva249@gmail.com', 'phone' => '+38 (050) 162-35-09'],
            ['id' => 26, 'user_id' => 26, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ольга', 'last_name' => 'Кобец', 'middle_name' => 'Николаевна', 'email' => 'user_297@factor.local', 'phone' => '+38 (066) 436-44-04'],
            ['id' => 27, 'user_id' => 27, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Сергей', 'last_name' => 'Величко', 'middle_name' => 'Сергеевич', 'email' => 'user_310@factor.local', 'phone' => '+38 (066) 215-02-45'],
            ['id' => 28, 'user_id' => 28, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ольга', 'last_name' => 'Сатановская', 'middle_name' => 'Викторовна', 'email' => 'satanovskaya.o@gmail.com', 'phone' => '+38 (096) 320-52-57'],
            ['id' => 29, 'user_id' => 29, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталия', 'last_name' => 'Иваницкая', 'middle_name' => 'Анатольевна', 'email' => '06090413n@gmail.com', 'phone' => '+38 (063) 497-18-27'],
            ['id' => 30, 'user_id' => 30, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Киприянова', 'middle_name' => 'Валерьевна', 'email' => 'kipriyanovatanya81@gmail.com', 'phone' => '+38 (093) 102-26-22'],
            ['id' => 31, 'user_id' => 31, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Денис', 'last_name' => 'Станов', 'middle_name' => 'Михайлович', 'email' => 'Stanovd@gmail.com', 'phone' => '+38 (063) 635-55-55'],
            ['id' => 32, 'user_id' => 32, 'office_id' => 23, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталия', 'last_name' => 'Шарапова', 'middle_name' => 'Ивановна', 'email' => '7952055odessa@gmail.com', 'phone' => '+38 (068) 792-17-92'],
            ['id' => 33, 'user_id' => 33, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталья', 'last_name' => 'Макарова', 'middle_name' => 'Владимировна', 'email' => 'natamak4@gmail.com', 'phone' => '+38 (067) 921-18-14'],
            ['id' => 34, 'user_id' => 34, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталья', 'last_name' => 'Буцкая', 'middle_name' => 'Марковна', 'email' => 'Natabutskaya@gmail.com', 'phone' => '+38 (067) 796-41-85'],
            ['id' => 35, 'user_id' => 35, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Дмитрий', 'last_name' => 'Соколов', 'middle_name' => 'Константинович', 'email' => 'user_343@factor.local', 'phone' => '+38 (096) 121-57-23'],
            ['id' => 36, 'user_id' => 36, 'office_id' => 23, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Владимир', 'last_name' => 'Минеско', 'middle_name' => 'Григорьевич', 'email' => 'vovamenesko.com@gmail.com', 'phone' => '+38 (096) 044-88-93'],
            ['id' => 37, 'user_id' => 37, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алевтина', 'last_name' => 'Компаниец', 'middle_name' => 'Владимировна', 'email' => 'alevtinavjik@gmail.com', 'phone' => '+38 (099) 743-27-44'],
            ['id' => 38, 'user_id' => 38, 'office_id' => 23, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Мария', 'last_name' => 'Митрофанова', 'middle_name' => 'Николаевна', 'email' => 'm2998805@gmail.com', 'phone' => '+38 (050) 021-75-99'],
            ['id' => 39, 'user_id' => 39, 'office_id' => 20, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Михова', 'middle_name' => 'Юрьевна', 'email' => 'user_360@factor.local', 'phone' => '+38 (066) 995-33-27'],
            ['id' => 40, 'user_id' => 40, 'office_id' => 20, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Малыхина', 'middle_name' => 'Ивановна', 'email' => 'user_361@factor.local', 'phone' => '+38 (096) 898-83-88'],
            ['id' => 41, 'user_id' => 41, 'office_id' => 23, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Давыденко', 'middle_name' => 'Анатольевна', 'email' => 'user_370@factor.local', 'phone' => '+38 (097) 897-13-31'],
            ['id' => 42, 'user_id' => 42, 'office_id' => 21, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анна', 'last_name' => 'Супенко', 'middle_name' => 'Викторовна', 'email' => 'genation182@gmail.com', 'phone' => '+38 (063) 885-32-07'],
            ['id' => 43, 'user_id' => 43, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алла', 'last_name' => 'Сулименко', 'middle_name' => 'Ивановна', 'email' => 'alla_sulimenko@ukr.net', 'phone' => '+38 (097) 227-28-76'],
            ['id' => 44, 'user_id' => 44, 'office_id' => 21, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Инна', 'last_name' => 'Бернатович', 'middle_name' => 'Александровна', 'email' => 'bernarovichinna@gmail.com', 'phone' => '+38 (073) 032-36-34'],
            ['id' => 45, 'user_id' => 45, 'office_id' => 21, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Ружицкая', 'middle_name' => 'Петровна', 'email' => 'tetianaruzhytska777@gmail.com', 'phone' => '+38 (097) 591-56-58'],
            ['id' => 46, 'user_id' => 46, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лилия', 'last_name' => 'Южбабенко', 'middle_name' => '', 'email' => 'uzbabenkolilia@gmail.com', 'phone' => '+38 (098) 429-50-55'],
            ['id' => 47, 'user_id' => 47, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анна', 'last_name' => 'Игнат', 'middle_name' => 'Георгиевна', 'email' => 'user_388@factor.local', 'phone' => '+38 (068) 502-30-75'],
            ['id' => 48, 'user_id' => 48, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алла', 'last_name' => 'Сокол', 'middle_name' => 'Александровна', 'email' => 'user_389@factor.local', 'phone' => '+38 (098) 322-95-08'],
            ['id' => 49, 'user_id' => 49, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Кравцова', 'middle_name' => 'Олеговна', 'email' => 'Kravt_sova@ukr.net', 'phone' => '+38 (073) 592-12-84'],
            ['id' => 50, 'user_id' => 50, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Вознюк', 'middle_name' => 'Анатольевна', 'email' => 'zakolka7km@gmail.com', 'phone' => '+38 (099) 311-59-10'],
            ['id' => 51, 'user_id' => 51, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Виктория', 'last_name' => 'Калаченко', 'middle_name' => 'Валериевна', 'email' => 'Kalachenkovika@gmail.com', 'phone' => '+38 (098) 464-46-55'],
            ['id' => 52, 'user_id' => 52, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Валентина', 'last_name' => 'Прозорова', 'middle_name' => '', 'email' => 'user_395@factor.local', 'phone' => '+38 (096) 407-11-90'],
            ['id' => 53, 'user_id' => 53, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Чернышенко', 'middle_name' => 'Николаевна', 'email' => 'user_397@factor.local', 'phone' => '+38 (066) 265-45-04'],
            ['id' => 54, 'user_id' => 54, 'office_id' => 7, 'position_id' => 329, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Великсарь', 'middle_name' => 'Валерьевна', 'email' => 'user_403@factor.local', 'phone' => '+38 (068) 455-02-03'],
            ['id' => 55, 'user_id' => 55, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Николай', 'last_name' => 'Гавриленко', 'middle_name' => 'Юрьевич', 'email' => 'user_405@factor.local', 'phone' => '+38 (068) 080-85-08'],
            ['id' => 56, 'user_id' => 56, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Елена', 'last_name' => 'Цалькер', 'middle_name' => 'Алексеевна', 'email' => 'user_411@factor.local', 'phone' => '+38 (067) 483-10-23'],
            ['id' => 57, 'user_id' => 57, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Евгения', 'last_name' => 'Онгьор', 'middle_name' => 'Васильевна', 'email' => 'user_418@factor.local', 'phone' => '+38 (063) 224-02-30'],
            ['id' => 58, 'user_id' => 58, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Евгения', 'last_name' => 'Федорова', 'middle_name' => 'Сергеевна', 'email' => 'user_427@factor.local', 'phone' => '+38 (050) 602-93-29'],
            ['id' => 59, 'user_id' => 59, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ярослав', 'last_name' => 'Белущенко', 'middle_name' => 'Сергеевич', 'email' => 'belushchenko89@gmail.com', 'phone' => '+38 (099) 036-36-45'],
            ['id' => 60, 'user_id' => 60, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ольга', 'last_name' => 'Катеринич', 'middle_name' => 'Сергеевна', 'email' => 'user_436@factor.local', 'phone' => '+38 (098) 852-98-04'],
            ['id' => 61, 'user_id' => 61, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лиля', 'last_name' => 'Ковалева', 'middle_name' => 'Леонидовна', 'email' => 'lilay1954lidia@gmail.com', 'phone' => '+38 (096) 533-54-97'],
            ['id' => 62, 'user_id' => 62, 'office_id' => 9, 'position_id' => 329, 'status_id' => 333, 'first_name' => 'Лариса', 'last_name' => 'Деликатная', 'middle_name' => 'Александровна', 'email' => 'user_441@factor.local', 'phone' => '+38 (095) 586-46-77'],
            ['id' => 63, 'user_id' => 63, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алена', 'last_name' => 'Матвеева', 'middle_name' => 'Петровна', 'email' => 'user_443@factor.local', 'phone' => '+38 (099) 431-45-45'],
            ['id' => 64, 'user_id' => 64, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Катерина', 'last_name' => 'Фактор 9', 'middle_name' => '', 'email' => 'user_445@factor.local', 'phone' => '+38 (073) 355-85-76'],
            ['id' => 65, 'user_id' => 65, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Роман', 'last_name' => 'Диденкул', 'middle_name' => 'Георгиевич', 'email' => 'realtorodessaroman@gmail.com', 'phone' => '+38 (067) 559-08-83'],
            ['id' => 66, 'user_id' => 66, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Дарья', 'last_name' => 'Радева', 'middle_name' => 'Юрьевна', 'email' => 'user_468@factor.local', 'phone' => '+38 (096) 072-35-10'],
            ['id' => 67, 'user_id' => 67, 'office_id' => 10, 'position_id' => 329, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Свиридчинкова', 'middle_name' => '', 'email' => 'user_470@factor.local', 'phone' => '+38 (096) 385-65-60'],
            ['id' => 68, 'user_id' => 68, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Вера', 'last_name' => 'Кинда', 'middle_name' => 'Владимировна', 'email' => 'Verakinda2@gmail.com', 'phone' => '+38 (097) 116-25-73'],
            ['id' => 69, 'user_id' => 69, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Светлана', 'last_name' => 'Згурская', 'middle_name' => 'Олеговна', 'email' => 'Svetasdnfch312503@gmail.com', 'phone' => '+38 (073) 131-03-00'],
            ['id' => 70, 'user_id' => 70, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Александр', 'last_name' => 'Ильченко', 'middle_name' => 'Викторович', 'email' => 'Ilcenkoa773@gmail.com', 'phone' => '+38 (068) 535-36-88'],
            ['id' => 71, 'user_id' => 71, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Ганчева', 'middle_name' => 'Ивановна', 'email' => 'irinahancheva@gmail.com', 'phone' => '+38 (097) 992-69-63'],
            ['id' => 72, 'user_id' => 72, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Богдан', 'last_name' => 'Чуниховский', 'middle_name' => 'Артемович', 'email' => 'bchunikhovskyi@gmail.com', 'phone' => '+38 (099) 257-40-55'],
            ['id' => 73, 'user_id' => 73, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лилия', 'last_name' => 'Грек', 'middle_name' => 'Федоровна', 'email' => 'user_489@factor.local', 'phone' => '+38 (096) 634-69-64'],
            ['id' => 74, 'user_id' => 74, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Светлана', 'last_name' => 'Островская', 'middle_name' => 'Ивановна', 'email' => 'Vetaverb@gmail.com', 'phone' => '+38 (050) 788-03-94'],
            ['id' => 75, 'user_id' => 75, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анастасия', 'last_name' => 'Ивко', 'middle_name' => '', 'email' => 'gofmanodessa@gmail.com', 'phone' => '+38 (096) 565-01-54'],
            ['id' => 76, 'user_id' => 76, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Марина', 'last_name' => 'Прокопенко', 'middle_name' => 'Владимировна', 'email' => 'mgrnstaeva@ukr.net', 'phone' => '+38 (068) 688-88-78'],
            ['id' => 77, 'user_id' => 77, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лиля', 'last_name' => 'Крацик', 'middle_name' => 'Михайловна', 'email' => 'liliya.vld@gmail.com', 'phone' => '+38 (067) 558-89-84'],
            ['id' => 78, 'user_id' => 78, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алена', 'last_name' => 'Барданова', 'middle_name' => 'Васильевна', 'email' => 'alonabardanova@gmail.com', 'phone' => '+38 (097) 147-72-58'],
            ['id' => 79, 'user_id' => 79, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Марианна', 'last_name' => 'Стрембицкая', 'middle_name' => 'Федоровна', 'email' => 'm.strembytska@gmail.com', 'phone' => '+38 (068) 235-91-10'],
            ['id' => 80, 'user_id' => 80, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ирина', 'last_name' => 'Рамизова', 'middle_name' => 'Евгеньевна', 'email' => 'user_497@factor.local', 'phone' => '+38 (073) 501-00-30'],
            ['id' => 81, 'user_id' => 81, 'office_id' => 11, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Александр', 'last_name' => 'Кучменко', 'middle_name' => 'Георгиевич', 'email' => 'user_498@factor.local', 'phone' => '+38 (099) 896-65-84'],
            ['id' => 82, 'user_id' => 82, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Виктория', 'last_name' => 'Костенко', 'middle_name' => '', 'email' => 'user_499@factor.local', 'phone' => '+38 (063) 622-64-11'],
            ['id' => 83, 'user_id' => 83, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Оксана', 'last_name' => 'Попова', 'middle_name' => 'Анатольевна', 'email' => 'xenapopova1980@gmail.com', 'phone' => '+38 (099) 017-54-94'],
            ['id' => 84, 'user_id' => 84, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ксения', 'last_name' => 'Давыдова', 'middle_name' => 'Андреевна', 'email' => 'dksuha82@gmail.com', 'phone' => '+38 (068) 821-82-14'],
            ['id' => 85, 'user_id' => 85, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Денис', 'last_name' => 'Марченко', 'middle_name' => 'Николаевич', 'email' => 'palma.manager01@gmail.com', 'phone' => '+38 (066) 262-52-30'],
            ['id' => 86, 'user_id' => 86, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Валентина', 'last_name' => 'Верба', 'middle_name' => 'Борисовна', 'email' => 'user_506@factor.local', 'phone' => '+38 (067) 923-18-91'],
            ['id' => 87, 'user_id' => 87, 'office_id' => 5, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Иван', 'last_name' => 'Солодчук', 'middle_name' => 'Николаевич', 'email' => 'solodokhcrv@gmail.com', 'phone' => '+38 (093) 020-89-93'],
            ['id' => 88, 'user_id' => 88, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Николай', 'last_name' => 'Мельщик', 'middle_name' => 'Александрович', 'email' => 'odessamail2205@gmail.com', 'phone' => '+38 (067) 487-80-31'],
            ['id' => 89, 'user_id' => 89, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Ангелина', 'last_name' => 'Борсук', 'middle_name' => 'Васильевна', 'email' => 'angelinaborsuk5480@gmail.com', 'phone' => '+38 (098) 394-14-76'],
            ['id' => 90, 'user_id' => 90, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лилия', 'last_name' => 'Власюк', 'middle_name' => 'Евгеньевна', 'email' => 'liliavlasuk75@gmail.com', 'phone' => '+38 (095) 110-63-81'],
            ['id' => 91, 'user_id' => 91, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Светлана', 'last_name' => 'Копсова', 'middle_name' => 'Михайловна', 'email' => 'Smkopsova@gmail.com', 'phone' => '+38 (095) 819-50-96'],
            ['id' => 92, 'user_id' => 92, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Юлианна', 'last_name' => 'Осадчук', 'middle_name' => '', 'email' => 'Lifebettystyle@gmail.com', 'phone' => '+38 (073) 827-21-55'],
            ['id' => 93, 'user_id' => 93, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Алена', 'last_name' => 'Игнатенко', 'middle_name' => 'Викторовна', 'email' => 'Ispravnikova_aliona@ukr.net', 'phone' => '+38 (066) 990-76-15'],
            ['id' => 94, 'user_id' => 94, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Карина', 'last_name' => 'Черевко', 'middle_name' => 'Игоревна', 'email' => 'user_516@factor.local', 'phone' => '+38 (096) 052-52-88'],
            ['id' => 95, 'user_id' => 95, 'office_id' => 7, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Татьяна', 'last_name' => 'Жук', 'middle_name' => 'Борисовна', 'email' => 'tatianazhuk75@gmail.com', 'phone' => '+38 (050) 908-88-77'],
            ['id' => 96, 'user_id' => 96, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Лолита', 'last_name' => 'Коваленко', 'middle_name' => 'Валерьевна', 'email' => 'lolitakovalenko95@gmail.com', 'phone' => '+38 (099) 963-20-45'],
            ['id' => 97, 'user_id' => 97, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Александр', 'last_name' => 'Кулик', 'middle_name' => 'Александрович', 'email' => 'user_519@factor.local', 'phone' => '+38 (073) 479-93-51'],
            ['id' => 98, 'user_id' => 98, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Анастасия', 'last_name' => 'Кульчицкая', 'middle_name' => 'Викторовна', 'email' => 'user_520@factor.local', 'phone' => '+38 (096) 936-01-00'],
            ['id' => 99, 'user_id' => 99, 'office_id' => 8, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Александра', 'last_name' => 'Ткачук', 'middle_name' => 'Александровна', 'email' => 'user_521@factor.local', 'phone' => '+38 (068) 376-06-00'],
            ['id' => 100, 'user_id' => 100, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталья', 'last_name' => 'Кобак', 'middle_name' => 'Петровна', 'email' => 'user_522@factor.local', 'phone' => '+38 (093) 900-90-55'],
            ['id' => 101, 'user_id' => 101, 'office_id' => 9, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Александр', 'last_name' => 'Чернега', 'middle_name' => 'Иванович', 'email' => 'user_523@factor.local', 'phone' => '+38 (067) 483-05-57'],
            ['id' => 102, 'user_id' => 102, 'office_id' => 10, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Катерина', 'last_name' => 'Гуцу', 'middle_name' => 'Викторовна', 'email' => 'user_524@factor.local', 'phone' => '+38 (099) 029-34-06'],
            ['id' => 103, 'user_id' => 103, 'office_id' => 6, 'position_id' => 327, 'status_id' => 333, 'first_name' => 'Наталия', 'last_name' => 'Загиченко', 'middle_name' => 'Васильевна', 'email' => 'user_525@factor.local', 'phone' => '+38 (067) 317-64-00'],
        ];

        foreach ($employees as $empData) {
            $id = $empData['id'];
            unset($empData['id']);

            Employee::updateOrCreate(
                ['id' => $id],
                array_merge($empData, [
                    'company_id' => $company->id,
                    'is_active' => true,
                ])
            );
        }
    }
}
