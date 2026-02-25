<?php

namespace App\Services\XmlExport\Mappings;

class RemMappings
{
    /**
     * Dictionary deal_type name → rem.ua <type> value
     */
    public const DEAL_TYPE_MAP = [
        'Продажа' => 'продажа',
        'Аренда'  => 'аренда',
    ];

    /**
     * Dictionary deal_type name → verb for title generation
     */
    public const TITLE_VERB_MAP = [
        'Продажа' => 'Продам',
        'Аренда'  => 'Сдам',
    ];

    /**
     * Dictionary property_type name → rem.ua <category> value
     * Допускаются: квартира, торговые помещения, коммерция, дом, участок
     */
    public const CATEGORY_MAP = [
        'Квартира'              => 'квартира',
        'Дом'                   => 'дом',
        'Участок'               => 'участок',
        'Торговое помещение'    => 'торговые помещения',
        'Торговые помещения'    => 'торговые помещения',
        'Коммерция'             => 'коммерция',
        'Коммерческая'          => 'коммерция',
        'Офис'                  => 'коммерция',
    ];

    /**
     * Currency code → rem.ua currency value
     * Допускаются: UAH, USD
     */
    public const CURRENCY_MAP = [
        'USD' => 'USD',
        'UAH' => 'UAH',
    ];

    public static function mapDealType(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::DEAL_TYPE_MAP[$name] ?? null;
    }

    public static function mapCategory(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::CATEGORY_MAP[$name] ?? null;
    }

    public static function mapCurrency(?string $code): ?string
    {
        if ($code === null) {
            return null;
        }

        return self::CURRENCY_MAP[$code] ?? $code;
    }

    /**
     * Build title from deal type and property type.
     * Example: "Продажа" + "Квартира" → "Продам квартиру"
     */
    public static function buildTitle(?string $dealTypeName, ?string $propertyTypeName): ?string
    {
        $verb = self::TITLE_VERB_MAP[$dealTypeName] ?? null;

        if ($verb === null) {
            return null;
        }

        $category = self::mapCategory($propertyTypeName);

        if ($category === null) {
            return $verb;
        }

        // "Продам" + "квартиру" / "Сдам" + "квартиру"
        $accusative = self::toAccusative($category);

        return $verb . ' ' . $accusative;
    }

    /**
     * Simple nominative → accusative conversion for property categories.
     */
    private static function toAccusative(string $category): string
    {
        return match ($category) {
            'квартира'            => 'квартиру',
            'дом'                 => 'дом',
            'участок'             => 'участок',
            'торговые помещения'  => 'торговое помещение',
            'коммерция'           => 'коммерцию',
            default               => $category,
        };
    }
}
