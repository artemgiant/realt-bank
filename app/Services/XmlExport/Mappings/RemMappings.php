<?php

namespace App\Services\XmlExport\Mappings;

class RemMappings
{
    /**
     * Dictionary property_type name → rem.ua <category> value
     * Допускаются: квартира, торговые помещения, коммерция, дом, участок
     */
    public const CATEGORY_MAP = [
        // Квартиры
        'Квартира'              => 'квартира',
        'Пентхаус'              => 'квартира',
        'Квартира на земле'     => 'квартира',
        'Студия'                => 'квартира',
        'Комната'               => 'квартира',

        // Дома
        'Дом'                   => 'дом',
        'Таунхаус'              => 'дом',
        'Дуплекс'               => 'дом',
        'Часть дома'            => 'дом',
        'Коттедж'               => 'дом',

        // Участки
        'Земля под жилую застройку'              => 'участок',
        'Земля коммерческого назначения'          => 'участок',
        'Земля сельскохозяйственного назначения'  => 'участок',

        // Торговые помещения
        'Торговое помещение'    => 'торговые помещения',

        // Коммерция
        'Офисное помещение'                => 'коммерция',
        'Складские помещения'              => 'коммерция',
        'Производственные помещения'       => 'коммерция',
        'Помещение свободного назначения'  => 'коммерция',
        'Здание'                           => 'коммерция',
        'Готовый бизнес'                   => 'коммерция',
        'Паркинг'                          => 'коммерция',
        'Машино-место'                     => 'коммерция',
        'Гараж'                            => 'коммерция',
        'Бокс'                             => 'коммерция',
    ];

    /**
     * Category + deal type → rem.ua URL segment
     * Формат: '{category}' => ['продажа' => '{slug}', 'аренда' => '{slug}-rent']
     */
    public const URL_SEGMENT_MAP = [
        'квартира'            => ['продажа' => 'apartments', 'аренда' => 'apartments-rent'],
        'дом'                 => ['продажа' => 'houses',     'аренда' => 'houses-rent'],
        'коммерция'           => ['продажа' => 'commercial', 'аренда' => 'commercial-rent'],
        'торговые помещения'  => ['продажа' => 'commercial', 'аренда' => 'commercial-rent'],
        'участок'             => ['продажа' => 'earth',      'аренда' => 'earth-rent'],
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

        if (str_starts_with($name, 'Продажа')) {
            return 'продажа';
        }

        if (str_starts_with($name, 'Аренда')) {
            return 'аренда';
        }

        return null;
    }

    public static function mapCategory(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        return self::CATEGORY_MAP[$name] ?? null;
    }

    /**
     * Map category + deal type to rem.ua URL segment.
     * Example: ('квартира', 'продажа') → 'apartments'
     */
    public static function mapUrlSegment(?string $category, ?string $dealType): ?string
    {
        if ($category === null || $dealType === null) {
            return null;
        }

        return self::URL_SEGMENT_MAP[$category][$dealType] ?? null;
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
        if ($dealTypeName === null) {
            return null;
        }

        if (str_starts_with($dealTypeName, 'Продажа')) {
            $verb = 'Продам';
        } elseif (str_starts_with($dealTypeName, 'Аренда')) {
            $verb = 'Сдам';
        } else {
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
