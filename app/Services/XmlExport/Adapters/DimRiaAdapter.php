<?php

namespace App\Services\XmlExport\Adapters;

use App\Services\XmlExport\Dto\PropertyExportData;
use App\Services\XmlExport\Mappings\DimRiaMappings;
use Spatie\ArrayToXml\ArrayToXml;

class DimRiaAdapter extends AbstractXmlAdapter
{
    public function getName(): string
    {
        return 'dim_ria';
    }

    public function getRootElement(): string
    {
        return 'realty';
    }

    /**
     * @param PropertyExportData[] $items
     */
    public function generateBatchXml(array $items): string
    {
        $batchData = [];

        foreach ($items as $dto) {
            $batchData[] = $this->filterEmpty($this->toArray($dto));
        }

        return ArrayToXml::convert(
            [
                'generation_date' => now()->format('Y-m-d\TH:i:sP'),
                'lang_id'         => 2,
                'realty'           => $batchData,
            ],
            [
                'rootElementName' => 'realties',
                '_attributes' => [
                    'xmlns'              => 'https://dom.ria.com/xml/xsd/',
                    'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'https://dom.ria.com/xml/xsd/ https://dom.ria.com/xml/xsd/dom.xsd',
                ],
            ],
            xmlEncoding: 'UTF-8',
        );
    }

    public function validate(PropertyExportData $dto): array
    {
        $missing = [];

        if (empty($dto->email)) $missing[] = 'email';
        if (empty($dto->phone)) $missing[] = 'phone';
        if (empty($dto->stateName)) $missing[] = 'state';
        if (empty($dto->cityName)) $missing[] = 'city';
        if (empty($dto->streetName)) $missing[] = 'street';
        if (empty($dto->buildingNumber)) $missing[] = 'building_number';
        if (empty($dto->roomCountValue)) $missing[] = 'rooms_count';
        if (empty($dto->areaTotal)) $missing[] = 'total_area';
        if (empty($dto->floorsTotal)) $missing[] = 'floors';
        if (empty($dto->floor)) $missing[] = 'floor';
        if (empty($dto->price)) $missing[] = 'price';
        if (empty($dto->currencySymbol)) $missing[] = 'currency';

        return $missing;
    }

    public function toArray(PropertyExportData $dto): array
    {
        $features = DimRiaMappings::mapFeatures($dto->featureNames);
        $heatingFields = DimRiaMappings::mapHeatingFields($dto->heatingTypeName);

        return [
            // Базовые параметры (константы)
            'advert_type'     => 'Продажа',
            'realty_type'     => 'Квартира',
            'realty_sale_type' => 1,

            // Данные риелтора
            'email'           => $dto->email,
            'phone'           => self::formatPhone($dto->phone),
            'local_realty_id' => $dto->id,

            // Фото
            'photos_urls' => [
                'loc' => $dto->photoUrls,
            ],

            // Видео
            'youtube_link' => $dto->youtubeUrl,
            'tiktok_link'  => $dto->tiktokUrl,

            // Расположение
            'state'            => $dto->stateName,
            'city'             => $dto->cityName,
            'district'         => $dto->districtName,
            'street'           => $dto->streetName,
            'street_type'      => 'улица',
            'building_number'  => $dto->buildingNumber,
            'show_building_no' => 1,
            'show_flat_no'     => 1,
            'flat_number_str'  => $dto->apartmentNumber,
            'radius_location'  => 'да',

            // Характеристики
            'characteristics' => array_merge(
                [
                    // Информация о доме
                    'wall_type'   => DimRiaMappings::mapWallType($dto->wallTypeName),
                    'build_year'  => $dto->yearBuilt,
                    'parking'     => DimRiaMappings::mapParking($dto->featureNames),

                    // Основные параметры
                    'rooms_count'    => $dto->roomCountValue,
                    'total_area'     => $dto->areaTotal,
                    'living_area'    => $dto->areaLiving,
                    'kitchen_area'   => $dto->areaKitchen,
                    'floors'         => $dto->floorsTotal,
                    'floor'          => $dto->floor,
                    'flat_state'     => DimRiaMappings::mapCondition($dto->conditionName),
                    'ceiling_height' => $dto->ceilingHeightValue,

                    // Тип дома
                    'building_type' => DimRiaMappings::mapBuildingType($dto->buildingTypeName),

                    // Цена
                    'price_type'  => 'за объект',
                    'price'       => $dto->price ? (int) $dto->price : null,
                    'currency'    => DimRiaMappings::mapCurrency($dto->currencySymbol),
                    'offer_type'  => 'от посредника',
                ],
                // Отопление (да/нет поля)
                $heatingFields,
                // Features → boolean поля (да/нет)
                $features,
            ),
        ];
    }

    /**
     * Format phone to +38 (0XX) XXX-XX-XX
     */
    private static function formatPhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        // Ensure 12 digits starting with 38: 380XXXXXXXXX
        if (str_starts_with($digits, '38') && strlen($digits) === 12) {
            $code = substr($digits, 2, 3);  // 0XX
            $part1 = substr($digits, 5, 3);
            $part2 = substr($digits, 8, 2);
            $part3 = substr($digits, 10, 2);

            return "+38 ({$code}) {$part1}-{$part2}-{$part3}";
        }

        return $phone;
    }
}
