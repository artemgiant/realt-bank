<?php

namespace App\Services\Migration\Migrators;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Property\Property;
use Illuminate\Support\Facades\DB;

/**
 * Мигратор контактов: JSON data (name_sale, telephone) → contacts + contact_phones + contactables.
 *
 * Для каждого объекта:
 * 1. Если есть имя или телефон продавца — создаём/находим Contact
 * 2. Привязываем к Property через contactables (полиморфная связь)
 * 3. Дедупликация по телефону: если контакт с таким телефоном уже есть — используем его
 *
 * Маппинг type_sale → role:
 *   1 = Риелтор
 *   2 = Продавец (Собственник)
 */
class ContactMigrator
{
    // Кеш: нормализованный телефон → contact_id (для дедупликации)
    protected array $phoneCache = [];

    // Маппинг type_sale → role в contactables
    protected const TYPE_SALE_ROLES = [
        1 => 'Риелтор',
        2 => 'Продавец',
    ];

    // Статистика
    protected array $stats = [
        'created' => 0,
        'reused' => 0,
        'skipped' => 0,
    ];

    /**
     * Перенос контакта для одного объекта.
     */
    public function migrateForProperty(Property $property, ?object $data, ?int $typeSale): void
    {
        if (!$data) return;

        // Основной контакт (name_sale + telephone)
        $this->processContact(
            $property,
            $data->name_sale ?? null,
            $data->telephone ?? null,
            $typeSale
        );

        // Второй контакт (name_sale_2) — телефон берём из old_telephone если есть
        if (!empty($data->name_sale_2)) {
            $this->processContact(
                $property,
                $data->name_sale_2,
                null, // нет отдельного телефона для 2-го контакта
                null
            );
        }
    }

    /**
     * Обработка одного контакта: поиск или создание + привязка к property.
     */
    protected function processContact(
        Property $property,
        ?string $name,
        ?string $phone,
        ?int $typeSale
    ): void {
        // Пропускаем если нет ни имени, ни телефона
        if (empty($name) && empty($phone)) {
            $this->stats['skipped']++;
            return;
        }

        $normalizedPhone = $this->normalizePhone($phone);
        $contact = null;

        // Дедупликация по телефону
        if ($normalizedPhone) {
            // Сначала проверяем кеш
            if (isset($this->phoneCache[$normalizedPhone])) {
                $contact = Contact::find($this->phoneCache[$normalizedPhone]);
            }

            // Потом ищем в БД
            if (!$contact) {
                $contactPhone = ContactPhone::where('phone', $normalizedPhone)->first();
                if ($contactPhone) {
                    $contact = $contactPhone->contact;
                    $this->phoneCache[$normalizedPhone] = $contact->id;
                }
            }
        }

        // Создаём новый контакт если не нашли
        if (!$contact) {
            $contact = Contact::create([
                'first_name' => ucfirst(trim($name ?? 'Без имени')),
                'company_id' => 1, // Factor (компания по умолчанию)
            ]);

            // Создаём телефон
            if ($normalizedPhone) {
                ContactPhone::create([
                    'contact_id' => $contact->id,
                    'phone' => $normalizedPhone,
                    'is_primary' => true,
                ]);
                $this->phoneCache[$normalizedPhone] = $contact->id;
            }

            $this->stats['created']++;
        } else {
            $this->stats['reused']++;
        }

        // Привязываем к property через contactables
        $role = self::TYPE_SALE_ROLES[$typeSale ?? 0] ?? null;

        // Проверяем что связь ещё не существует
        $exists = DB::table('contactables')
            ->where('contact_id', $contact->id)
            ->where('contactable_type', Property::class)
            ->where('contactable_id', $property->id)
            ->exists();

        if (!$exists) {
            DB::table('contactables')->insert([
                'contact_id' => $contact->id,
                'contactable_type' => Property::class,
                'contactable_id' => $property->id,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Нормализация телефона: убираем всё кроме цифр.
     */
    protected function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $digits = preg_replace('/\D/', '', $phone);
        return !empty($digits) ? $digits : null;
    }

    public function getStats(): array
    {
        return $this->stats;
    }
}
