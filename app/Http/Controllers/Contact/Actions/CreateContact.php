<?php

namespace App\Http\Controllers\Contact\Actions;

use App\Helpers\PhoneFormatter;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Reference\Dictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика создания контакта.
 *
 * Вся операция обёрнута в транзакцию:
 * нормализация тегов, загрузка фото, создание записи,
 * сохранение телефонов, привязка к объекту, синхронизация ролей.
 */
class CreateContact
{
    /**
     * Создать контакт со всеми связями.
     *
     * @param array   $data    Валидированные данные из StoreContactRequest
     * @param Request $request Исходный запрос (для файлов)
     * @return Contact Созданный контакт с загруженными связями
     *
     * @throws \Exception В случае ошибки — транзакция откатывается, загруженное фото удаляется
     */
    public function execute(array $data, Request $request): Contact
    {
        // Проверка дубля по телефону — если контакт уже существует, возвращаем его
        $existingContact = $this->findExistingByPhone($data['phones'] ?? []);
        if ($existingContact) {
            return $this->updateExisting($existingContact, $data, $request);
        }

        $photoPath = null;

        try {
            DB::beginTransaction();

            // Нормализация tags (companies отправляет массив ID, остальные — строку)
            $tags = $data['tags'] ?? null;
            if (is_array($tags)) {
                $tagNames = Dictionary::whereIn('id', $tags)->pluck('name');
                $tags = $tagNames->implode(',');
            }

            // Загрузка фото
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('contacts/photos', 'public');
            }

            // Определение ролей
            $roles = $data['roles'] ?? $data['contact_role'] ?? [];

            // Создание контакта
            $contact = Contact::create([
                'company_id'   => $data['company_id'],
                'first_name'   => $data['first_name'],
                'last_name'    => $data['last_name'] ?? null,
                'middle_name'  => $data['middle_name'] ?? null,
                'email'        => $data['email'] ?? null,
                'contact_role' => $roles,
                'tags'         => $tags,
                'telegram'     => $data['telegram'] ?? null,
                'viber'        => $data['viber'] ?? null,
                'whatsapp'     => $data['whatsapp'] ?? null,
                'passport'     => $data['passport'] ?? null,
                'inn'          => $data['inn'] ?? null,
                'comment'      => $data['comment'] ?? null,
                'photo'        => $photoPath,
            ]);

            // Сохранение телефонов
            $this->savePhones($contact, $data['phones']);

            // Генерация уникального хеша (id + телефоны)
            $contact->refreshPhoneHash();

            // Привязка к объекту (если передан property_id)
            if (!empty($data['property_id'])) {
                $contact->properties()->attach($data['property_id']);
            }

            // Синхронизация ролей
            $contact->roles()->sync($roles);

            DB::commit();

            // Загружаем связи для ответа
            $contact->load(['phones', 'roles']);

            return $contact;

        } catch (\Exception $e) {
            DB::rollBack();

            // Удаляем загруженное фото если была ошибка
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $e;
        }
    }

    /**
     * Сохранение телефонов контакта.
     * Удаляет старые телефоны и создаёт новые.
     * Если основной телефон не указан — первый становится основным.
     */
    private function savePhones(Contact $contact, array $phones): void
    {
        $contact->phones()->delete();

        $hasPrimary = collect($phones)->contains(fn($p) => !empty($p['is_primary']));

        foreach ($phones as $index => $phoneData) {
            ContactPhone::create([
                'contact_id' => $contact->id,
                'phone'      => PhoneFormatter::format($phoneData['phone']),
                'is_primary' => !empty($phoneData['is_primary']) || (!$hasPrimary && $index === 0),
            ]);
        }
    }

    /**
     * Поиск существующего контакта по номеру телефона.
     * Проверяет все переданные телефоны — если хотя бы один найден в БД, возвращает контакт.
     */
    private function findExistingByPhone(array $phones): ?Contact
    {
        foreach ($phones as $phoneData) {
            $phone = $phoneData['phone'] ?? null;
            if (!$phone) continue;

            $formatted = PhoneFormatter::format($phone);
            $existing = ContactPhone::where('phone', $formatted)->first();
            if ($existing) {
                return $existing->contact;
            }
        }

        return null;
    }

    /**
     * Обновление существующего контакта новыми данными (вместо создания дубля).
     * Обновляет роли и привязку к объекту, возвращает контакт с загруженными связями.
     */
    private function updateExisting(Contact $contact, array $data, Request $request): Contact
    {
        $roles = $data['roles'] ?? $data['contact_role'] ?? [];

        // Синхронизируем роли (добавляем новые, не удаляя старые)
        if (!empty($roles)) {
            $existingRoles = $contact->roles()->pluck('dictionaries.id')->toArray();
            $mergedRoles = array_unique(array_merge($existingRoles, (array) $roles));
            $contact->roles()->sync($mergedRoles);
        }

        // Привязка к объекту
        if (!empty($data['property_id'])) {
            $contact->properties()->syncWithoutDetaching([$data['property_id']]);
        }

        $contact->load(['phones', 'roles']);

        return $contact;
    }
}
