<?php

namespace App\Http\Controllers\Location\Presenters;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\Street;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Форматирование данных локаций для JSON-ответов.
 *
 * Преобразует модели локаций в массивы для API.
 * НЕ делает запросы к БД — работает только с загруженными данными.
 */
class LocationPresenter
{
    /**
     * Форматировать улицу в массив для JSON-ответа.
     * Используется в поиске улиц и получении улицы по ID.
     *
     * @param Street $street Модель улицы с загруженными связями (city, district, zone)
     * @return array Отформатированные данные улицы
     */
    public function formatStreet(Street $street): array
    {
        return [
            'id' => $street->id,
            'name' => $street->name,
            'city_id' => $street->city_id,
            'city_name' => $street->city?->name,
            'district_id' => $street->district_id,
            'district_name' => $street->district?->name,
            'zone_id' => $street->zone_id,
            'zone_name' => $street->zone?->name,
            'full_address' => $street->full_address,
            'short_address' => $street->short_address,
        ];
    }

    /**
     * Форматировать регион (область) в массив для JSON-ответа.
     * Используется в поиске регионов, получении по умолчанию и по ID.
     *
     * @param State $state Модель региона с загруженной связью (country)
     * @return array Отформатированные данные региона
     */
    public function formatState(State $state): array
    {
        return [
            'id' => $state->id,
            'name' => $state->name,
            'code' => $state->code,
            'country_id' => $state->country_id,
            'country_name' => $state->country?->name,
            'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
        ];
    }

    /**
     * Универсальный поиск по всем типам локаций (страна, область, город).
     * Возвращает объединённый и отсортированный список результатов.
     *
     * @param Request $request HTTP-запрос с параметрами q и limit
     * @return array Массив результатов поиска
     */
    public function formatSearchAllResults(Request $request): array
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 20);

        $results = collect();

        // Поиск по странам
        $countries = Country::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'type' => 'country',
                    'name' => $country->name,
                    'full_name' => $country->name,
                    'parent' => null,
                ];
            });
        $results = $results->merge($countries);

        // Поиск по областям
        $states = State::with('country')
            ->active()
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($state) {
                return [
                    'id' => $state->id,
                    'type' => 'state',
                    'name' => $state->name,
                    'full_name' => $state->name . ', ' . ($state->country?->name ?? ''),
                    'parent' => $state->country?->name,
                    'country_id' => $state->country_id,
                ];
            });
        $results = $results->merge($states);

        // Поиск по городам
        $cities = City::with(['state', 'state.country'])
            ->active()
            ->where('name', 'like', "%{$query}%")
            ->limit(15)
            ->get()
            ->map(function ($city) {
                $parent = $city->state?->name;
                if ($city->state?->country) {
                    $parent .= ', ' . $city->state->country->name;
                }
                return [
                    'id' => $city->id,
                    'type' => 'city',
                    'name' => $city->name,
                    'full_name' => $city->name . ', ' . $parent,
                    'parent' => $parent,
                    'state_id' => $city->state_id,
                    'country_id' => $city->state?->country_id,
                ];
            });
        $results = $results->merge($cities);

        // Сортируем и ограничиваем
        return $results->take($limit)->values()->all();
    }
}
