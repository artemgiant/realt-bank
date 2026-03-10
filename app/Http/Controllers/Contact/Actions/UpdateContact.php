<?php

namespace App\Http\Controllers\Contact\Actions;

use App\Helpers\PhoneFormatter;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Бизнес-логика обновления контакта.
 *
 * Вся операция обёрнута в транзакцию:
 * обработка фото (удаление/замена), обновление записи,
 * сохранение телефонов, синхронизация ролей.
 */
class UpdateContact
{
    /**
     * Обновить контакт со всеми связями.
     *
     * @param Contact $contact Обновляемый контакт
     * @param array   $data    Валидированные данные из UpdateContactRequest
     * @param Request $request Исходный запрос (для файлов)
     * @return Contact Обновлённый контакт с загруженными связями
     *
     * @throws \Exception В случае ошибки — транзакция откатывается
     */
    public function execute(Contact $contact, array $data, Request $request): Contact
    {
        return DB::transaction(function () use ($contact, $data, $request) {
            // Обработка фото
            $photoPath = $contact->photo;

            // Удаление фото
            if ($request->boolean('remove_photo') && $contact->photo) {
                Storage::disk('public')->delete($contact->photo);
                $photoPath = null;
            }

            // Загрузка нового фото
            if ($request->hasFile('photo')) {
                // Удаляем старое фото
                if ($contact->photo) {
                    Storage::disk('public')->delete($contact->photo);
                }
                $photoPath = $request->file('photo')->store('contacts/photos', 'public');
            }

            // Определение ролей
            $roles = $data['roles'] ?? $data['contact_role'] ?? [];

            // Обновление контакта
            $contact->update([
                'company_id'   => $data['company_id'],
                'first_name'   => $data['first_name'],
                'last_name'    => $data['last_name'] ?? null,
                'middle_name'  => $data['middle_name'] ?? null,
                'email'        => $data['email'] ?? null,
                'contact_role' => $roles,
                'tags'         => $data['tags'] ?? null,
                'telegram'     => $data['telegram'] ?? null,
                'viber'        => $data['viber'] ?? null,
                'whatsapp'     => $data['whatsapp'] ?? null,
                'passport'     => $data['passport'] ?? null,
                'inn'          => $data['inn'] ?? null,
                'comment'      => $data['comment'] ?? null,
                'photo'        => $photoPath,
            ]);

            // Обновление телефонов
            $this->savePhones($contact, $data['phones']);

            // Обновление хеша телефонов
            $contact->refreshPhoneHash();

            // Синхронизация ролей
            $contact->roles()->sync($roles);

            // Загружаем связи для ответа
            $contact->load(['phones', 'roles']);

            return $contact;
        });
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
}
