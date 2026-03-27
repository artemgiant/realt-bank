<?php

namespace App\Http\Controllers\Property\Property\Presenters;

use App\Models\Property\Property;
use App\Models\Reference\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Форматирование данных объекта недвижимости для таблицы DataTables.
 *
 * Преобразует модель Property в массив для JSON-ответа.
 * НЕ делает запросы к БД — работает только с загруженными данными.
 */
class PropertyTablePresenter
{
    /**
     * Форматировать один объект в строку таблицы DataTables.
     * Включает основные данные + данные для раскрывающейся строки (child row).
     */
    public function toRow(Property $property, ?Currency $targetCurrency = null): array
    {
        return [
            'id' => $property->id,
            'user_id' => $property->user_id,
            'owner_company_id' => $property->employee?->company_id,
            'owner_office_id' => $property->employee?->office_id ?? $property->user?->employee?->office_id,
            'checkbox' => $property->id,
            'deal_type' => $property->dealType?->name ?? '-',
            'location' => $this->location($property),
            'property_type' => $property->propertyType?->name ?? '-',
            'room_count' => $property->roomCount?->name ?? null,
            'wall_type' => $property->wallType?->name ?? null,
            'building_type' => $property->buildingType?->name ?? null,
            'area' => [
                'total' => $property->area_total ? ceil($property->area_total) : null,
                'living' => $property->area_living ? ceil($property->area_living) : null,
                'kitchen' => $property->area_kitchen ? ceil($property->area_kitchen) : null,
            ],
            'area_land' => $property->area_land ? ceil($property->area_land) : null,
            'price_per_m2' => $this->pricePerM2($property, $targetCurrency),
            'condition' => $property->condition?->name ?? '-',
            'floor' => $this->floor($property),
            'photo' => $this->photo($property),
            'price' => $this->price($property, $targetCurrency),
            'commission' => $property->commission,
            'contact' => $this->contact($property),
            // Data for child row
            'title' => $property->getTranslation(app()->getLocale())?->title ?? $property->translations->first()?->title ?? '-',
            'description' => $property->getTranslation('ru')?->description ?? $property->translations->first()?->description ?? '-',
            'agent_notes' => $property->agent_notes,
            'personal_notes' => $property->personal_notes,
            'features' => $property->features->pluck('name')->toArray(),
            'youtube_url' => $property->youtube_url,
            'tiktok_url' => $property->tiktok_url,
            'created_at_formatted' => $property->created_at->format('d.m.Y'),
            'updated_at_formatted' => $property->updated_at->format('d.m.Y'),
            'updated_at' => $property->updated_at->toIso8601String(),
            // Block-info: contact vs agent visibility
            'is_visible_to_agents' => (bool) $property->is_visible_to_agents,
            'contact_for_display' => $this->contactForDisplay($property),
            'agent' => $this->agentForDisplay($property),
        ];
    }

    /**
     * Форматировать коллекцию объектов в массив строк таблицы.
     */
    public function toCollection(Collection $properties, ?Currency $targetCurrency = null): array
    {
        return $properties->map(fn(Property $property) => $this->toRow($property, $targetCurrency))->values()->all();
    }

    /**
     * Форматирование локации для таблицы.
     * Возвращает 3 строки: 1) ЖК (жирный), 2) Дом, Улица, Зона, 3) Район, Город, Область, Страна
     */
    private function location(Property $property): array
    {
        $complexName = $property->complex?->name ?? null;

        $streetParts = [];
        if ($property->building_number && $property->is_visible_to_agents) {
            $streetParts[] = $property->building_number;
        }
        if ($property->street) {
            $streetParts[] = $property->street->name;
        }
        if ($property->zone) {
            $streetParts[] = $property->zone->name;
        }
        $streetLine = !empty($streetParts) ? implode(', ', $streetParts) : null;

        $addressParts = [];
        if ($property->district) {
            $addressParts[] = $property->district->name;
        }
        if ($property->city) {
            $addressParts[] = $property->city->name;
        }
        if ($property->state) {
            $addressParts[] = $property->state->name;
        }
        if ($property->country) {
            $addressParts[] = $property->country->name;
        }
        $addressLine = !empty($addressParts) ? implode(', ', $addressParts) : null;

        $hasLocation = $complexName || $streetLine || $addressLine;

        return [
            'has_location' => $hasLocation,
            'complex' => $complexName,
            'street' => $streetLine,
            'address' => $addressLine,
        ];
    }

    /**
     * Форматирование этажа: "этаж/этажность", просто "этаж" или просто "этажность".
     */
    private function floor(Property $property): string
    {
        if ($property->floor && $property->floors_total) {
            return $property->floor . '/' . $property->floors_total;
        }

        if ($property->floor) {
            return (string) $property->floor;
        }

        if ($property->floors_total) {
            return (string) $property->floors_total;
        }

        return '-';
    }

    /**
     * Форматирование фото: главное фото + массив всех фото для галереи.
     */
    private function photo(Property $property): array|string
    {
        $photos = $property->photos->sortBy('sort_order');

        if ($photos->isEmpty()) {
            return '-';
        }

        $mainPhoto = $photos->firstWhere('is_main', true) ?? $photos->first();

        $resolveUrl = fn($path) => str_starts_with($path, 'http') ? $path : Storage::url($path);

        return [
            'main' => $resolveUrl($mainPhoto->path),
            'all' => $photos->map(fn($photo) => $resolveUrl($photo->path))->values()->toArray(),
        ];
    }

    /**
     * Форматирование цены с конвертацией валюты.
     * Конвертация: Цена * КурсОбъекта / КурсЦелевойВалюты
     */
    private function price(Property $property, ?Currency $targetCurrency = null): string
    {
        if (!$property->price) {
            return '-';
        }

        if ($targetCurrency && $property->currency) {
            $priceInUah = $property->price * $property->currency->rate;
            $convertedPrice = $priceInUah / $targetCurrency->rate;

            $symbol = $targetCurrency->symbol;
            return number_format($convertedPrice, 0, '.', ' ') . ' ' . $symbol;
        }

        $symbol = $property->currency?->symbol ?? '$';
        return number_format($property->price, 0, '.', ' ') . ' ' . $symbol;
    }

    /**
     * Форматирование цены за м² с конвертацией валюты.
     */
    private function pricePerM2(Property $property, ?Currency $targetCurrency = null): ?string
    {
        if (!$property->price_per_m2) {
            return null;
        }

        if ($targetCurrency && $property->currency) {
            $priceM2InUah = $property->price_per_m2 * $property->currency->rate;
            $convertedPriceM2 = $priceM2InUah / $targetCurrency->rate;

            return number_format($convertedPriceM2, 0, '.', ' ');
        }

        return number_format($property->price_per_m2, 0, '.', ' ');
    }

    /**
     * Форматирование агента (сотрудника) для колонки таблицы.
     */
    private function contact(Property $property): array
    {
        $employee = $property->employee;

        if (!$employee) {
            return [
                'has_contact' => false,
            ];
        }

        return [
            'has_contact' => true,
            'full_name' => $employee->full_name,
            'contact_role_names' => $property->contactType?->name,
            'phone' => $employee->phone,
            'company_name' => $employee->company?->name,
            'position_name' => $employee->position?->name,
        ];
    }

    /**
     * Контакты (клиенты) объекта для раскрывающейся строки (child row).
     */
    private function contactForDisplay(Property $property): ?array
    {
        $contacts = $property->contacts;
        if ($contacts->isEmpty()) {
            return null;
        }
        return $contacts->map(fn($contact) => [
            'full_name' => $contact->full_name,
            'contact_role_names' => $contact->contact_role_names ?? '-',
            'roles_names' => $contact->roles_names ?? '-',
            'phone' => $contact->primary_phone ?? '-',
        ])->values()->all();
    }

    /**
     * Агент (сотрудник) для раскрывающейся строки (child row).
     */
    private function agentForDisplay(Property $property): ?array
    {
        $employee = $property->employee;
        if (!$employee) {
            return null;
        }
        return [
            'full_name' => $employee->full_name,
            'company_name' => $employee->company?->name ?? '',
            'phone' => $employee->phone ?? '-',
            'photo_url' => $employee->photo_url,
        ];
    }
}
