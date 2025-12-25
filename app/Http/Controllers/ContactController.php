<?php

namespace App\Http\Controllers;

use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Property\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Поиск контактов для select2 / autocomplete
     * GET /contacts/ajax-search?q=поисковый_запрос
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $search = $request->input('q', '');
        $limit = $request->input('limit', 20);

        $query = Contact::with('phones');

        if (!empty($search)) {
            $query->search($search);
        }

        $contacts = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit($limit)
            ->get();

        // Формат для select2
        $results = $contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'text' => $contact->full_name . ' (' . ($contact->primary_phone ?? '-') . ')',
                'full_name' => $contact->full_name,
                'phone' => $contact->primary_phone,
                'email' => $contact->email,
                'contact_type' => $contact->contact_type,
                'contact_type_name' => $contact->contact_type_name,
            ];
        });

        return response()->json([
            'results' => $results,
            'total' => $contacts->count(),
        ]);
    }

    /**
     * Поиск контакта по номеру телефона
     * GET /contacts/ajax-search-by-phone?phone=+380...
     */
    public function ajaxSearchByPhone(Request $request): JsonResponse
    {
        $phone = $request->input('phone', '');

        // Очищаем номер от лишних символов для поиска
        $phoneClean = preg_replace('/[^0-9+]/', '', $phone);

        if (strlen($phoneClean) < 6) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'Номер телефона слишком короткий',
            ]);
        }

        // Ищем контакт по номеру телефона
        $contactPhone = ContactPhone::where('phone', 'like', '%' . $phoneClean . '%')
            ->orWhere('phone', 'like', '%' . $phone . '%')
            ->first();

        if (!$contactPhone) {
            return response()->json([
                'success' => true,
                'found' => false,
                'message' => 'Контакт не найден',
            ]);
        }

        $contact = $contactPhone->contact;
        $contact->load('phones');

        return response()->json([
            'success' => true,
            'found' => true,
            'message' => 'Контакт найден',
            'contact' => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'middle_name' => $contact->middle_name,
                'full_name' => $contact->full_name,
                'short_name' => $contact->short_name,
                'email' => $contact->email,
                'contact_type' => $contact->contact_type,
                'contact_type_name' => $contact->contact_type_name,
                'tags' => $contact->tags,
                'telegram' => $contact->telegram,
                'viber' => $contact->viber,
                'whatsapp' => $contact->whatsapp,
                'passport' => $contact->passport,
                'inn' => $contact->inn,
                'comment' => $contact->comment,
                'photo' => $contact->photo,
                'photo_url' => $contact->photo ? Storage::url($contact->photo) : null,
                'phones' => $contact->phones,
                'primary_phone' => $contact->primary_phone,
                'messengers' => $contact->messengers,
            ],
        ]);
    }

    /**
     * Создание контакта через AJAX (модальное окно)
     * POST /contacts/ajax-store
     */
    public function ajaxStore(Request $request): JsonResponse
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_type' => 'nullable|in:owner,agent,developer',
            'tags' => 'nullable|string|max:500',
            'telegram' => 'nullable|string|max:255',
            'viber' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'passport' => 'nullable|string|max:50',
            'inn' => 'nullable|string|max:20',
            'comment' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:2048',
            // Телефоны - массив
            'phones' => 'required|array|min:1',
            'phones.*.phone' => 'required|string|max:50',
            'phones.*.is_primary' => 'nullable|boolean',
            // Property ID для привязки (опционально)
            'property_id' => 'nullable|exists:properties,id',
        ], [
            'first_name.required' => 'Введите имя контакта',
            'phones.required' => 'Добавьте хотя бы один телефон',
            'phones.min' => 'Добавьте хотя бы один телефон',
            'phones.*.phone.required' => 'Введите номер телефона',
            'email.email' => 'Введите корректный email',
            'photo.image' => 'Файл должен быть изображением',
            'photo.max' => 'Максимальный размер фото 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Загрузка фото
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('contacts/photos', 'public');
            }

            // Создание контакта
            $contact = Contact::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'contact_type' => $request->contact_type,
                'tags' => $request->tags,
                'telegram' => $request->telegram,
                'viber' => $request->viber,
                'whatsapp' => $request->whatsapp,
                'passport' => $request->passport,
                'inn' => $request->inn,
                'comment' => $request->comment,
                'photo' => $photoPath,
            ]);

            // Создание телефонов
            $this->savePhones($contact, $request->phones);

            // Привязка к объекту (если передан property_id)
            if ($request->filled('property_id')) {
                $contact->properties()->attach($request->property_id);
            }

            DB::commit();

            // Загружаем связи для ответа
            $contact->load('phones');

            return response()->json([
                'success' => true,
                'message' => 'Контакт успешно создан',
                'contact' => [
                    'id' => $contact->id,
                    'full_name' => $contact->full_name,
                    'short_name' => $contact->short_name,
                    'primary_phone' => $contact->primary_phone,
                    'email' => $contact->email,
                    'contact_type' => $contact->contact_type,
                    'contact_type_name' => $contact->contact_type_name,
                    'photo_url' => $contact->photo ? Storage::url($contact->photo) : null,
                    'phones' => $contact->phones,
                    'messengers' => $contact->messengers,
                    'telegram_link' => $contact->telegram_link,
                    'viber_link' => $contact->viber_link,
                    'whatsapp_link' => $contact->whatsapp_link,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Удаляем загруженное фото если была ошибка
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Получение данных контакта через AJAX
     * GET /contacts/{contact}/ajax
     */
    public function ajaxShow(Contact $contact): JsonResponse
    {
        $contact->load('phones');

        return response()->json([
            'success' => true,
            'contact' => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'middle_name' => $contact->middle_name,
                'full_name' => $contact->full_name,
                'short_name' => $contact->short_name,
                'email' => $contact->email,
                'contact_type' => $contact->contact_type,
                'contact_type_name' => $contact->contact_type_name,
                'tags' => $contact->tags,
                'tags_array' => $contact->tags_array,
                'telegram' => $contact->telegram,
                'viber' => $contact->viber,
                'whatsapp' => $contact->whatsapp,
                'passport' => $contact->passport,
                'inn' => $contact->inn,
                'comment' => $contact->comment,
                'photo' => $contact->photo,
                'photo_url' => $contact->photo ? Storage::url($contact->photo) : null,
                'phones' => $contact->phones,
                'primary_phone' => $contact->primary_phone,
                'messengers' => $contact->messengers,
                'telegram_link' => $contact->telegram_link,
                'viber_link' => $contact->viber_link,
                'whatsapp_link' => $contact->whatsapp_link,
                'properties_count' => $contact->properties_count,
            ],
        ]);
    }

    /**
     * Обновление контакта через AJAX
     * PUT /contacts/{contact}/ajax
     */
    public function ajaxUpdate(Request $request, Contact $contact): JsonResponse
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_type' => 'nullable|in:owner,agent,developer',
            'tags' => 'nullable|string|max:500',
            'telegram' => 'nullable|string|max:255',
            'viber' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'passport' => 'nullable|string|max:50',
            'inn' => 'nullable|string|max:20',
            'comment' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:2048',
            'remove_photo' => 'nullable|boolean',
            'phones' => 'required|array|min:1',
            'phones.*.phone' => 'required|string|max:50',
            'phones.*.is_primary' => 'nullable|boolean',
        ], [
            'first_name.required' => 'Введите имя контакта',
            'phones.required' => 'Добавьте хотя бы один телефон',
            'phones.min' => 'Добавьте хотя бы один телефон',
            'phones.*.phone.required' => 'Введите номер телефона',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

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

            // Обновление контакта
            $contact->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'contact_type' => $request->contact_type,
                'tags' => $request->tags,
                'telegram' => $request->telegram,
                'viber' => $request->viber,
                'whatsapp' => $request->whatsapp,
                'passport' => $request->passport,
                'inn' => $request->inn,
                'comment' => $request->comment,
                'photo' => $photoPath,
            ]);

            // Обновление телефонов
            $this->savePhones($contact, $request->phones);

            DB::commit();

            $contact->load('phones');

            return response()->json([
                'success' => true,
                'message' => 'Контакт успешно обновлен',
                'contact' => [
                    'id' => $contact->id,
                    'full_name' => $contact->full_name,
                    'short_name' => $contact->short_name,
                    'primary_phone' => $contact->primary_phone,
                    'email' => $contact->email,
                    'contact_type' => $contact->contact_type,
                    'contact_type_name' => $contact->contact_type_name,
                    'photo_url' => $contact->photo ? Storage::url($contact->photo) : null,
                    'phones' => $contact->phones,
                    'messengers' => $contact->messengers,
                    'telegram_link' => $contact->telegram_link,
                    'viber_link' => $contact->viber_link,
                    'whatsapp_link' => $contact->whatsapp_link,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Удаление контакта через AJAX
     * DELETE /contacts/{contact}/ajax
     */
    public function ajaxDestroy(Contact $contact): JsonResponse
    {
        try {
            // Удаляем фото
            if ($contact->photo) {
                Storage::disk('public')->delete($contact->photo);
            }

            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Контакт успешно удален',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Привязать контакт к объекту
     * POST /properties/{property}/contacts
     */
    public function attachToProperty(Request $request, Property $property): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Проверяем, не привязан ли уже
            if ($property->contacts()->where('contact_id', $request->contact_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Контакт уже привязан к этому объекту',
                ], 422);
            }

            $property->contacts()->attach($request->contact_id);

            $contact = Contact::with('phones')->find($request->contact_id);

            return response()->json([
                'success' => true,
                'message' => 'Контакт привязан к объекту',
                'contact' => [
                    'id' => $contact->id,
                    'full_name' => $contact->full_name,
                    'short_name' => $contact->short_name,
                    'primary_phone' => $contact->primary_phone,
                    'email' => $contact->email,
                    'contact_type_name' => $contact->contact_type_name,
                    'photo_url' => $contact->photo ? Storage::url($contact->photo) : null,
                    'messengers' => $contact->messengers,
                    'telegram_link' => $contact->telegram_link,
                    'viber_link' => $contact->viber_link,
                    'whatsapp_link' => $contact->whatsapp_link,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при привязке контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Отвязать контакт от объекта
     * DELETE /properties/{property}/contacts/{contact}
     */
    public function detachFromProperty(Property $property, Contact $contact): JsonResponse
    {
        try {
            $property->contacts()->detach($contact->id);

            return response()->json([
                'success' => true,
                'message' => 'Контакт отвязан от объекта',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отвязке контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Сохранение телефонов контакта
     */
    protected function savePhones(Contact $contact, array $phones): void
    {
        // Удаляем старые телефоны
        $contact->phones()->delete();

        // Проверяем, есть ли основной телефон
        $hasPrimary = collect($phones)->contains(fn($p) => !empty($p['is_primary']));

        foreach ($phones as $index => $phoneData) {
            ContactPhone::create([
                'contact_id' => $contact->id,
                'phone' => $phoneData['phone'],
                // Если основной не указан - первый телефон становится основным
                'is_primary' => !empty($phoneData['is_primary']) || (!$hasPrimary && $index === 0),
            ]);
        }
    }
}
