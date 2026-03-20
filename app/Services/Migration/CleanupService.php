<?php

namespace App\Services\Migration;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Сервис очистки целевых таблиц перед повторной миграцией из factor_dump.
 *
 * Очищает только те таблицы, в которые мы переносим данные.
 * НЕ трогает: users, справочники (dictionaries), локации, роли.
 *
 * Вызывается из artisan-команды: app:migrate-from-factor-dump --fresh
 */
class CleanupService
{
    protected ?OutputStyle $output;

    /**
     * Таблицы для полной очистки (TRUNCATE).
     * Порядок не важен — FK_CHECKS отключаются.
     */
    protected const TRUNCATE_TABLES = [
        // Объекты недвижимости и всё связанное
        'property_photos',       // фото объектов
        'property_features',     // характеристики (many-to-many)
        'property_translations', // переводы описаний
        'property_documents',    // документы
        'properties',            // сами объекты

        // Контакты (все привязаны к объектам, не к пользователям)
        'contactables',          // связующая таблица (полиморфная)
        'contact_phones',        // телефоны контактов
        'contacts',              // сами контакты

        // Компании и офисы (будут пересозданы из filials)
        'company_offices',       // офисы
        'companies',             // компании
    ];

    public function __construct(?OutputStyle $output = null)
    {
        $this->output = $output;
    }

    /**
     * Очистка целевых таблиц перед миграцией из factor_dump.
     *
     * Что сохраняется:
     * - users (6 записей — admin и созданные вручную)
     * - employees привязанные к users (обнуляем company_id/office_id)
     * - dictionaries (384 записи — справочники)
     * - локации: cities, districts, zones, streets
     * - roles, permissions, model_has_roles
     * - currencies, sources
     *
     * Что удаляется:
     * - properties, photos, features, translations, documents
     * - contacts, contactables, contact_phones
     * - employees без user_id
     * - companies, company_offices
     *
     * @return array Статистика: кол-во удалённых записей по каждой таблице
     */
    public function cleanup(): array
    {
        $stats = [];

        // Отключаем проверку FK чтобы можно было очищать таблицы в любом порядке
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Полная очистка целевых таблиц
        foreach (self::TRUNCATE_TABLES as $table) {
            $count = DB::table($table)->count();
            DB::table($table)->truncate();
            $stats[$table] = $count;
            $this->output?->writeln("  Truncated: {$table} ({$count} записей)");
        }

        // 2. Сотрудники: удаляем тех, что не привязаны к пользователям
        $deletedEmployees = DB::table('employees')->whereNull('user_id')->count();
        DB::table('employees')->whereNull('user_id')->delete();
        $stats['employees_deleted'] = $deletedEmployees;
        $this->output?->writeln("  Удалено сотрудников без user_id: {$deletedEmployees}");

        // 3. Оставшимся сотрудникам обнуляем company/office
        //    (будут заново привязаны после миграции филиалов)
        $keptEmployees = DB::table('employees')->count();
        DB::table('employees')->update(['company_id' => null, 'office_id' => null]);
        $stats['employees_kept'] = $keptEmployees;
        $this->output?->writeln("  Оставлено сотрудников (с user_id): {$keptEmployees}");

        // Роли пользователей (model_has_roles) НЕ трогаем —
        // у существующих users роли назначены вручную

        // Включаем обратно проверку FK
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $stats;
    }
}
