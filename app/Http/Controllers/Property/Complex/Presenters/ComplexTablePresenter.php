<?php

namespace App\Http\Controllers\Property\Complex\Presenters;

use App\Models\Reference\Complex;
use App\Models\Reference\Dictionary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Форматирование данных комплекса для таблицы DataTables.
 *
 * Преобразует модель Complex в массив для JSON-ответа.
 * НЕ делает запросы к БД — работает только с загруженными данными
 * (кроме Dictionary-запросов для conditions/features/object_types).
 */
class ComplexTablePresenter
{
    /**
     * Форматировать один комплекс в строку таблицы DataTables.
     * Включает основные данные + данные для раскрывающейся строки (child row).
     */
    public function toRow(Complex $complex): array
    {
        return [
            'id' => $complex->id,
            'checkbox' => $complex->id,

            // Локация
            'location' => $this->location($complex),

            // Типы объектов
            'property_type' => $this->objectTypes($complex),

            // Площадь
            'area' => [
                'from' => $complex->area_from,
                'to' => $complex->area_to,
            ],

            // Состояние
            'condition' => $this->condition($complex),

            // Этажность
            'floor' => $this->floorsRange($complex),

            // Фото
            'photo' => $this->photo($complex),

            // Цена
            'price' => $this->price($complex),

            // Контакт
            'contact' => $this->contact($complex),

            // Действия
            'actions' => null,

            // Данные для child row
            'description' => $complex->description,
            'agent_notes' => $complex->agent_notes,
            'special_conditions' => $complex->special_conditions,
            'features' => $this->featureNames($complex),
            'blocks' => $this->blocks($complex),
            'created_at' => $complex->created_at?->format('d.m.Y'),
            'updated_at' => $complex->updated_at?->format('d.m.Y'),
        ];
    }

    /** Форматировать коллекцию комплексов в массив строк таблицы */
    public function toCollection(Collection $complexes): array
    {
        return $complexes->map(fn(Complex $complex) => $this->toRow($complex))->values()->all();
    }

    // ========== Приватные методы форматирования ==========

    /** Форматирование локации: название, годы, улица, адрес */
    private function location(Complex $complex): array
    {
        $yearsStr = $this->yearsRange($complex);
        $block = $complex->blocks->first();

        return [
            'has_location' => true,
            'name' => $complex->name,
            'years' => $yearsStr ? "({$yearsStr})" : null,
            'street' => ($block && $block->street)
                ? ($block->street->name . ' ' . ($block->building_number ?? ''))
                : null,
            'address' => implode(', ', array_filter([
                $complex->district?->name,
                $complex->city?->name,
                $complex->city?->state?->name,
            ])),
        ];
    }

    /** Диапазон годов сдачи из блоков */
    private function yearsRange(Complex $complex): ?string
    {
        $years = $complex->blocks->pluck('year_built')->filter()->unique()->sort();

        if ($years->isEmpty()) {
            return null;
        }

        return $years->count() > 1
            ? $years->min() . '-' . $years->max()
            : (string) $years->first();
    }

    /** Диапазон этажности из блоков */
    private function floorsRange(Complex $complex): ?string
    {
        $floors = $complex->blocks->pluck('floors_total')->filter();

        if ($floors->isEmpty()) {
            return null;
        }

        return ($floors->min() === $floors->max())
            ? (string) $floors->min()
            : $floors->min() . '-' . $floors->max();
    }

    /** Названия типов объектов из JSON */
    private function objectTypes(Complex $complex): ?string
    {
        if (!$complex->object_types) {
            return null;
        }

        return Dictionary::whereIn('id', $complex->object_types)->pluck('name')->implode(', ');
    }

    /** Состояние и типы стен */
    private function condition(Complex $complex): array
    {
        $conditionNames = [];
        if ($complex->conditions) {
            $conditionIds = is_array($complex->conditions) ? $complex->conditions : json_decode($complex->conditions, true);
            if ($conditionIds) {
                $conditionNames = Dictionary::whereIn('id', $conditionIds)->pluck('name')->toArray();
            }
        }

        $wallTypes = $complex->blocks->pluck('wallType.name')->filter()->unique()->toArray();

        return [
            'conditions' => $conditionNames,
            'wall_type' => implode(', ', $wallTypes) ?: null,
        ];
    }

    /** Первое фото комплекса */
    private function photo(Complex $complex): ?string
    {
        if ($complex->photos && count($complex->photos) > 0) {
            return Storage::url($complex->photos[0]);
        }

        return null;
    }

    /** Форматирование цены */
    private function price(Complex $complex): array
    {
        $currency = $complex->currency ?? 'USD';

        return [
            'total' => $complex->price_total
                ? number_format($complex->price_total, 0, '', ' ') . ' ' . $currency
                : null,
            'per_m2' => $complex->price_per_m2
                ? number_format($complex->price_per_m2, 0, '', ' ') . ' ' . $currency
                : null,
        ];
    }

    /** Первый контакт комплекса */
    private function contact(Complex $complex): array
    {
        $contact = $complex->contacts->first();
        $contactPhone = ($contact && $contact->phones->isNotEmpty())
            ? $contact->phones->first()->phone
            : null;

        return [
            'has_contact' => $contact !== null,
            'full_name' => $contact?->full_name,
            'contact_role_names' => $contact?->contact_role_names,
            'phone' => $contactPhone,
        ];
    }

    /** Названия особенностей из JSON */
    private function featureNames(Complex $complex): array
    {
        if (!$complex->features) {
            return [];
        }

        $featureIds = is_array($complex->features) ? $complex->features : json_decode($complex->features, true);
        if (!$featureIds) {
            return [];
        }

        return Dictionary::whereIn('id', $featureIds)->pluck('name')->toArray();
    }

    /** Блоки комплекса для child row */
    private function blocks(Complex $complex): array
    {
        return $complex->blocks->map(fn($block) => [
            'id' => $block->id,
            'name' => $block->name,
            'address' => $block->street
                ? ($block->street->name . ' ' . ($block->building_number ?? ''))
                : null,
            'wall_type' => $block->wallType?->name,
            'heating_type' => $block->heatingType?->name,
            'floors' => $block->floors_total,
            'year_built' => $block->year_built,
            'photo' => $block->plan_path ? Storage::url($block->plan_path) : null,
        ])->toArray();
    }
}
