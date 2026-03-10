<?php

namespace App\Http\Controllers\Contact\Presenters;

use App\Models\Contact\Contact;
use Illuminate\Support\Facades\Storage;

/**
 * Форматирование данных контакта для JSON-ответов.
 *
 * НЕ делает запросы к БД — работает только с загруженными данными.
 * Два формата: полный (для показа/редактирования) и краткий (для списков/карточек).
 */
class ContactPresenter
{
    /**
     * Полный формат контакта для ajaxShow (просмотр/редактирование).
     * Включает все поля, телефоны, роли, мессенджеры, паспорт, ИНН и т.д.
     */
    public function toFullResponse(Contact $contact): array
    {
        return [
            'id'                 => $contact->id,
            'first_name'         => $contact->first_name,
            'last_name'          => $contact->last_name,
            'middle_name'        => $contact->middle_name,
            'full_name'          => $contact->full_name,
            'short_name'         => $contact->short_name,
            'email'              => $contact->email,
            'contact_role'       => $contact->contact_role,
            'contact_role_names' => $contact->contact_role_names,
            'roles_names'        => $contact->roles_names,
            'roles'              => $contact->roles->pluck('id')->toArray(),
            'tags'               => $contact->tags,
            'tags_array'         => $contact->tags_array,
            'telegram'           => $contact->telegram,
            'viber'              => $contact->viber,
            'whatsapp'           => $contact->whatsapp,
            'passport'           => $contact->passport,
            'inn'                => $contact->inn,
            'comment'            => $contact->comment,
            'photo'              => $contact->photo,
            'photo_url'          => $contact->photo ? Storage::url($contact->photo) : null,
            'phones'             => $contact->phones,
            'primary_phone'      => $contact->primary_phone,
            'messengers'         => $contact->messengers,
            'telegram_link'      => $contact->telegram_link,
            'viber_link'         => $contact->viber_link,
            'whatsapp_link'      => $contact->whatsapp_link,
            'properties_count'   => $contact->properties_count,
        ];
    }

    /**
     * Краткий формат контакта для ответов ajaxStore, ajaxUpdate, attachToProperty.
     * Содержит основные данные для отображения в карточке/списке.
     */
    public function toShortResponse(Contact $contact): array
    {
        return [
            'id'                 => $contact->id,
            'full_name'          => $contact->full_name,
            'short_name'         => $contact->short_name,
            'primary_phone'      => $contact->primary_phone,
            'email'              => $contact->email,
            'contact_role'       => $contact->contact_role,
            'contact_role_names' => $contact->contact_role_names,
            'roles_names'        => $contact->roles_names,
            'photo_url'          => $contact->photo ? Storage::url($contact->photo) : null,
            'phones'             => $contact->phones,
            'messengers'         => $contact->messengers,
            'telegram_link'      => $contact->telegram_link,
            'viber_link'         => $contact->viber_link,
            'whatsapp_link'      => $contact->whatsapp_link,
        ];
    }
}
