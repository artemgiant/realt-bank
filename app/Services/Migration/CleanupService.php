<?php

namespace App\Services\Migration;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Сервис очистки целевых таблиц перед повторной миграцией из factor_dump.
 *
 * Очищает все таблицы, в которые мы переносим данные.
 * НЕ трогает: справочники (dictionaries), локации, определения ролей (roles, permissions).
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
        'property_features',     // характеристики (many-to-many)
        'property_translations', // переводы описаний
        'property_documents',    // документы
        'properties',            // сами объекты

        // Контакты (все привязаны к объектам, не к пользователям)
        'contactables',          // связующая таблица (полиморфная)
        'contact_phones',        // телефоны контактов
        'contacts',              // сами контакты

        // Пользователи и сотрудники (пересоздаются из factor_dump)
        'model_has_roles',       // роли пользователей (spatie)
        'employees',             // сотрудники
        'users',                 // пользователи

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
     * - dictionaries (справочники)
     * - локации: cities, districts, zones, streets
     * - roles, permissions (определения ролей, не привязки)
     * - currencies, sources
     *
     * Что удаляется:
     * - properties, photos, features, translations, documents
     * - contacts, contactables, contact_phones
     * - users, employees, model_has_roles
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

        // Включаем обратно проверку FK
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $stats;
    }
}
