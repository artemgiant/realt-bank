<?php

namespace App\Services\XmlExport\Adapters;

use App\Helpers\PhoneFormatter;
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

        if (empty($dto->phone)) {
            $missing[] = 'phone';
        } else {
            $formatted = PhoneFormatter::format($dto->phone);
            if (!PhoneFormatter::isUkrainianFormat($formatted)) {
                $missing[] = "phone (неверный формат: {$formatted}, нужен +38 (0XX) XXX-XX-XX)";
            }
        }
        if (empty($dto->stateName)) $missing[] = 'state';
        if (empty($dto->cityName)) $missing[] = 'city';
        if (empty($dto->streetName)) $missing[] = 'street';
        if (empty($dto->price)) $missing[] = 'price';
        if (empty($dto->currencySymbol)) $missing[] = 'currency';
        if (count($dto->photoUrls) < 1) $missing[] = 'photos_urls (минимум 1 фото)';

        $isLand = str_contains($dto->propertyTypeName ?? '', 'Земля');
        $isHouse = in_array($dto->propertyTypeName, ['Дом', 'Таунхаус', 'Дуплекс', 'Коттедж', 'Часть дома']);

        if (!$isLand) {
            if (empty($dto->areaTotal)) $missing[] = 'total_area';
            if (empty($dto->floorsTotal)) $missing[] = 'floors';
            if (empty($dto->roomCountValue)) $missing[] = 'rooms_count';
        }

        if (!$isLand && !$isHouse) {
            if (empty($dto->floor)) $missing[] = 'floor';
        }

        return $missing;
    }

    private function ensureMinPhotos(array $photos, int $min): array
    {
        if (empty($photos)) {
            return $photos;
        }

        $lastPhoto = end($photos);
        while (count($photos) < $min) {
            $photos[] = $lastPhoto;
        }

        return $photos;
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
            'phone'           => $dto->phone ? PhoneFormatter::format($dto->phone) : null,
            'local_realty_id' => $dto->id,

            // Фото (минимум 5, дублируем последнюю если не хватает)
            'photos_urls' => [
                'loc' => $this->ensureMinPhotos($dto->photoUrls, 5),
            ],

            // Видео
            'youtube_link' => $dto->youtubeUrl,
            'tiktok_link'  => $dto->tiktokUrl,

            // Расположение
            'state'            => preg_replace('/\s*область$/iu', '', $dto->stateName),
            'city'             => $dto->cityName,
            'district'         => $dto->districtName,
            'street'           => $dto->streetName,
            'street_type'      => 'улица',
            'building_number'   => $dto->buildingNumber,
            'show_building_no'  => 0,
            'radius_location'   => 'да',

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

}
