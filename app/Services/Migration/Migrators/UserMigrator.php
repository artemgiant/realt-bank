<?php

namespace App\Services\Migration\Migrators;

use App\Helpers\PhoneFormatter;
use App\Models\Employee\Employee;
use App\Models\Reference\Dictionary;
use App\Models\User;
use App\Services\Migration\Mappers\FilialMapper;
use App\Services\Migration\Mappers\UserMapper;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;

/**
 * Мигратор пользователей: factor_dump.users → users + employees + роли.
 *
 * Шаг 2 миграции (после филиалов, до объектов).
 * Для каждого старого пользователя (deleted=0):
 *   1. Создаёт User (name, email, phone, password)
 *   2. Назначает spatie-роль (role_id 1 → super_admin, остальные → agent)
 *   3. Создаёт Employee (привязка к компании, офису, должности)
 *   4. Сохраняет маппинг old user_id → new user_id
 */
class UserMigrator
{
    protected UserMapper $userMapper;
    protected FilialMapper $filialMapper;
    protected ?OutputStyle $output;

    // Соответствие старых ролей новым spatie-ролям
    // role_id=1 → super_admin (администратор), остальные → agent (агент)
    protected const ROLE_MAP = [
        1 => 'super_admin',
        2 => 'agent',
        3 => 'agent',
        4 => 'agent',
    ];

    public function __construct(
        UserMapper $userMapper,
        FilialMapper $filialMapper,
        ?OutputStyle $output = null
    ) {
        $this->userMapper = $userMapper;
        $this->filialMapper = $filialMapper;
        $this->output = $output;
    }

    /**
     * Перенос пользователей.
     *
     * Маппинг полей:
     *   factor_dump.users.name + sname → users.name, employees.first_name + last_name
     *   factor_dump.users.email        → users.email, employees.email
     *   factor_dump.users.tel          → users.phone, employees.phone
     *   factor_dump.users.password     → users.password (уже хеширован)
     *   factor_dump.users.role_id      → spatie role (через ROLE_MAP)
     *   factor_dump.users.filial       → employees.office_id (через FilialMapper)
     */
    public function migrate(): array
    {
        $stats = ['created' => 0, 'skipped' => 0, 'errors' => 0];

        // Берём только не удалённых пользователей из старой базы
        $oldUsers = DB::connection('factor_dump')
            ->table('users')
            ->where('deleted', 0)
            ->get();

        // Ищем ID должности "Агент" и статуса "Активний" в справочниках
        // (нужны для создания Employee)
        $positionId = Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_POSITION)
            ->where('name', 'Агент')
            ->value('id');

        $activeStatusId = Dictionary::where('type', Dictionary::TYPE_EMPLOYEE_STATUS)
            ->where('name', 'Активний')
            ->value('id');

        $this->output?->info("Migrating {$oldUsers->count()} users...");

        foreach ($oldUsers as $oldUser) {
            try {
                // Подготовка email: если пустой — генерируем уникальный
                $email = $oldUser->email ?: null;
                $generatedEmail = 'user_' . $oldUser->id . '@factor.local';

                // Проверяем: может пользователь уже создан (повторный запуск)
                $existingUser = null;
                if ($email) {
                    $existingUser = User::where('email', $email)->first();
                }
                if (!$existingUser) {
                    $existingUser = User::where('email', $generatedEmail)->first();
                }

                if ($existingUser) {
                    // Пользователь уже существует — просто сохраняем маппинг
                    $this->userMapper->set($oldUser->id, $existingUser->id);
                    $stats['skipped']++;
                    continue;
                }

                // Email для нового пользователя
                if (!$email || User::where('email', $email)->exists()) {
                    $email = $generatedEmail;
                }

                // Форматируем телефон, если пустой или уже занят — генерируем уникальный
                $phone = !empty($oldUser->tel) ? PhoneFormatter::format($oldUser->tel) : null;
                if (!$phone || User::where('phone', $phone)->exists()) {
                    $phone = 'factor_' . $oldUser->id;
                }

                // 1. Создаём пользователя
                $user = new User();
                $firstName = mb_ucfirst(trim($oldUser->name ?? ''));
                $lastName = mb_ucfirst(trim($oldUser->sname ?? ''));
                $user->name = trim("$firstName $lastName");
                $user->email = $email;
                $user->phone = $phone;
                $user->password = $oldUser->password; // пароль уже хеширован в старой базе
                $user->is_active = true;
                $user->save();

                // 2. Назначаем роль через spatie/laravel-permission
                $roleName = self::ROLE_MAP[$oldUser->role_id] ?? 'agent';
                $user->assignRole($roleName);

                // 3. Создаём сотрудника (Employee) привязанного к компании и офису
                Employee::create([
                    'user_id' => $user->id,
                    'company_id' => $this->filialMapper->getCompanyId(),  // компания "Factor"
                    'office_id' => $this->filialMapper->get($oldUser->filial), // офис по старому filial_id
                    'position_id' => $positionId,       // должность "Агент"
                    'status_id' => $activeStatusId,     // статус "Активний"
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => mb_ucfirst(trim($oldUser->parent_name ?? '')),
                    'email' => $email,
                    'phone' => $phone,
                    'is_active' => true,
                ]);

                // 4. Сохраняем маппинг для PropertyMigrator
                $this->userMapper->set($oldUser->id, $user->id);
                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->output?->error("User #{$oldUser->id} ({$oldUser->login}): {$e->getMessage()}");
            }
        }

        $this->output?->info("Users migrated: {$stats['created']}, errors: {$stats['errors']}");

        return $stats;
    }
}
