<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Contact\Actions\CreateContact;
use App\Http\Controllers\Contact\Actions\DeleteContact;
use App\Http\Controllers\Contact\Actions\UpdateContact;
use App\Http\Controllers\Contact\Presenters\ContactPresenter;
use App\Http\Controllers\Contact\Requests\UpdateContactRequest;
use App\Http\Controllers\Controller;
use App\Helpers\PhoneFormatter;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactPhone;
use App\Models\Property\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Контроллер контактов — тонкий координатор.
 *
 * НЕ содержит бизнес-логику, НЕ форматирует данные.
 * Делегирует работу: Requests, Actions, Presenters.
 */
class ContactController extends Controller
{
    /**
     * Поиск контактов для select2 / autocomplete.
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
                'id'                 => $contact->id,
                'text'               => $contact->full_name . ' (' . ($contact->primary_phone ?? '-') . ')',
                'full_name'          => $contact->full_name,
                'phone'              => $contact->primary_phone,
                'email'              => $contact->email,
                'contact_role'       => $contact->contact_role,
                'contact_role_names' => $contact->contact_role_names,
            ];
        });

        return response()->json([
            'results' => $results,
            'total'   => $contacts->count(),
        ]);
    }

    /**
     * Поиск контакта по номеру телефона.
     * GET /contacts/ajax-search-by-phone?phone=+380...
     */
    public function ajaxSearchByPhone(Request $request): JsonResponse
    {
        $phone = $request->input('phone', '');

        // Оставляем только цифры для поиска (БД хранит в формате +38 (0XX) XXX-XX-XX)
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) < 9) {
            return response()->json([
                'success' => false,
                'found'   => false,
                'message' => 'Номер телефона слишком короткий',
            ]);
        }

        // Нормализуем до формата хранения через PhoneFormatter и ищем точное совпадение
        $formatted = PhoneFormatter::format($phone);
        $contactPhone = ContactPhone::where('phone', $formatted)->first();

        // Fallback: поиск по последним 9 цифрам (абонентский номер без кода страны)
        if (!$contactPhone) {
            $last9 = substr($digits, -9);
            $contactPhone = ContactPhone::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '(', ''), ')', ''), '-', '') LIKE ?", ['%' . $last9])
                ->first();
        }

        if (!$contactPhone) {
            return response()->json([
                'success' => true,
                'found'   => false,
                'message' => 'Контакт не найден',
            ]);
        }

        $contact = $contactPhone->contact;
        $contact->load('phones');

        $presenter = new ContactPresenter();

        return response()->json([
            'success' => true,
            'found'   => true,
            'message' => 'Контакт найден',
            'contact' => $presenter->toFullResponse($contact),
        ]);
    }

    /**
     * Создание контакта через AJAX (модальное окно).
     * POST /contacts/ajax-store
     * Валидация — StoreContactRequest, логика — CreateContact.
     */
    public function ajaxStore(StoreContactRequest $request, CreateContact $action): JsonResponse
    {
        try {
            $contact = $action->execute($request->validated(), $request);

            $presenter = new ContactPresenter();

            return response()->json([
                'success' => true,
                'message' => 'Контакт успешно создан',
                'contact' => $presenter->toShortResponse($contact),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Получение данных контакта через AJAX.
     * GET /contacts/{contact}/ajax
     */
    public function ajaxShow(Contact $contact): JsonResponse
    {
        $contact->load(['phones', 'roles']);

        $presenter = new ContactPresenter();

        return response()->json([
            'success' => true,
            'contact' => $presenter->toFullResponse($contact),
        ]);
    }

    /**
     * Обновление контакта через AJAX.
     * PUT /contacts/{contact}/ajax
     * Валидация — UpdateContactRequest, логика — UpdateContact.
     */
    public function ajaxUpdate(UpdateContactRequest $request, Contact $contact, UpdateContact $action): JsonResponse
    {
        try {
            $contact = $action->execute($contact, $request->validated(), $request);

            $presenter = new ContactPresenter();

            return response()->json([
                'success' => true,
                'message' => 'Контакт успешно обновлен',
                'contact' => $presenter->toShortResponse($contact),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Удаление контакта через AJAX.
     * DELETE /contacts/{contact}/ajax
     * Логика — DeleteContact.
     */
    public function ajaxDestroy(Contact $contact, DeleteContact $action): JsonResponse
    {
        try {
            $action->execute($contact);

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
     * Привязать контакт к объекту недвижимости.
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
                'errors'  => $validator->errors(),
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

            $presenter = new ContactPresenter();

            return response()->json([
                'success' => true,
                'message' => 'Контакт привязан к объекту',
                'contact' => $presenter->toShortResponse($contact),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при привязке контакта: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Отвязать контакт от объекта недвижимости.
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
}
