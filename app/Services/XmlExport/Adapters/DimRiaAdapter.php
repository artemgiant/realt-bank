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
        $category = DimRiaMappings::mapCategory($dto->propertyTypeName) ?? 'apartment';
        $minPhotos = DimRiaMappings::minPhotos($category);

        // Общие обязательные поля
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
        if (empty($dto->price)) $missing[] = 'price';
        if (empty($dto->currencySymbol)) $missing[] = 'currency';
        if (count($dto->photoUrls) < 1) $missing[] = "photos_urls (минимум 1 фото)";

        // По категориям
        switch ($category) {
            case 'apartment':
                if (empty($dto->streetName)) $missing[] = 'street';
                if (empty($dto->roomCountValue)) $missing[] = 'rooms_count';
                if (empty($dto->areaTotal)) $missing[] = 'total_area';
                if (empty($dto->floorsTotal)) $missing[] = 'floors';
                if (empty($dto->floor)) $missing[] = 'floor';
                break;

            case 'room':
                if (empty($dto->areaTotal)) $missing[] = 'total_area';
                if (empty($dto->floorsTotal)) $missing[] = 'floors';
                if (empty($dto->floor)) $missing[] = 'floor';
                break;

            case 'house':
                if (empty($dto->streetName)) $missing[] = 'street';
                if (empty($dto->roomCountValue)) $missing[] = 'rooms_count';
                if (empty($dto->areaTotal)) $missing[] = 'total_area';
                if (empty($dto->floorsTotal)) $missing[] = 'floors';
                break;

            case 'land':
                // Минимум обязательных — только общие
                break;

            case 'commercial':
                if (empty($dto->streetName)) $missing[] = 'street';
                if (empty($dto->areaTotal)) $missing[] = 'total_area';
                if (empty($dto->floorsTotal)) $missing[] = 'floors';
                if (empty($dto->floor)) $missing[] = 'floor';
                break;

            case 'garage':
                if (empty($dto->areaTotal)) $missing[] = 'total_area';
                break;
        }

        return $missing;
    }

    private function resolveCategory(PropertyExportData $dto): string
    {
        return DimRiaMappings::mapCategory($dto->propertyTypeName) ?? 'apartment';
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
        $category = $this->resolveCategory($dto);
        $minPhotos = DimRiaMappings::minPhotos($category);

        return [
            // Базовые параметры
            'advert_type'      => DimRiaMappings::mapAdvertType($dto->dealTypeName),
            'realty_type'      => DimRiaMappings::mapRealtyType($dto->propertyTypeName),
            'realty_sale_type' => 1,

            // Данные риелтора
            'phone'           => $dto->phone ? PhoneFormatter::format($dto->phone) : null,
            'local_realty_id' => $dto->id,

            // Фото (дублируем последнюю до минимума по категории)
            'photos_urls' => [
                'loc' => $this->ensureMinPhotos($dto->photoUrls, $minPhotos),
            ],

            // Видео
            'youtube_link' => $dto->youtubeUrl,
            'tiktok_link'  => $dto->tiktokUrl,

            // Расположение
            'state'             => preg_replace('/\s*область$/iu', '', $dto->stateName),
            'city'              => $dto->cityName,
            'district'          => $dto->districtName,
            'street'            => $dto->streetName,
            'street_type'       => 'улица',
            'building_number'   => $dto->buildingNumber,
            'show_building_no'  => 0,
            'radius_location'   => 'да',

            // Характеристики (зависят от категории)
            'characteristics' => $this->buildCharacteristics($dto, $category),
        ];
    }

    private function buildCharacteristics(PropertyExportData $dto, string $category): array
    {
        return match ($category) {
            'apartment' => $this->characteristicsApartment($dto),
            'room'      => $this->characteristicsRoom($dto),
            'house'     => $this->characteristicsHouse($dto),
            'land'      => $this->characteristicsLand($dto),
            'commercial'=> $this->characteristicsCommercial($dto),
            'garage'    => $this->characteristicsGarage($dto),
            default     => $this->characteristicsApartment($dto),
        };
    }

    private function characteristicsApartment(PropertyExportData $dto): array
    {
        return array_merge(
            [
                'wall_type'      => DimRiaMappings::mapWallType($dto->wallTypeName),
                'build_year'     => $dto->yearBuilt,
                'parking'        => DimRiaMappings::mapParking($dto->featureNames),
                'rooms_count'    => $dto->roomCountValue,
                'total_area'     => $dto->areaTotal,
                'living_area'    => $dto->areaLiving,
                'kitchen_area'   => $dto->areaKitchen,
                'floors'         => $dto->floorsTotal,
                'floor'          => $dto->floor,
                'flat_state'     => DimRiaMappings::mapCondition($dto->conditionName),
                'ceiling_height' => $dto->ceilingHeightValue,
                'building_type'  => DimRiaMappings::mapBuildingType($dto->buildingTypeName),
                'price_type'     => 'за объект',
                'price'          => $dto->price ? (int) $dto->price : null,
                'currency'       => DimRiaMappings::mapCurrency($dto->currencySymbol),
                'offer_type'     => 'от посредника',
            ],
            DimRiaMappings::mapHeatingFields($dto->heatingTypeName),
            DimRiaMappings::mapFeatures($dto->featureNames),
        );
    }

    private function characteristicsRoom(PropertyExportData $dto): array
    {
        return array_merge(
            [
                'wall_type'      => DimRiaMappings::mapWallType($dto->wallTypeName),
                'build_year'     => $dto->yearBuilt,
                'total_area'     => $dto->areaTotal,
                'living_area'    => $dto->areaLiving,
                'kitchen_area'   => $dto->areaKitchen,
                'floors'         => $dto->floorsTotal,
                'floor'          => $dto->floor,
                'flat_state'     => DimRiaMappings::mapCondition($dto->conditionName),
                'ceiling_height' => $dto->ceilingHeightValue,
                'price_type'     => 'за объект',
                'price'          => $dto->price ? (int) $dto->price : null,
                'currency'       => DimRiaMappings::mapCurrency($dto->currencySymbol),
                'offer_type'     => 'от посредника',
            ],
            DimRiaMappings::mapHeatingFields($dto->heatingTypeName),
            DimRiaMappings::mapFeatures($dto->featureNames),
        );
    }

    private function characteristicsHouse(PropertyExportData $dto): array
    {
        return array_merge(
            [
                'wall_type'      => DimRiaMappings::mapWallType($dto->wallTypeName),
                'build_year'     => $dto->yearBuilt,
                'rooms_count'    => $dto->roomCountValue,
                'total_area'     => $dto->areaTotal,
                'living_area'    => $dto->areaLiving,
                'kitchen_area'   => $dto->areaKitchen,
                'floors'         => $dto->floorsTotal,
                'plot_area'      => $dto->areaLand,
                'plot_area_unit' => $dto->areaLand ? 'сотка' : null,
                'house_state'    => DimRiaMappings::mapHouseState($dto->conditionName),
                'ceiling_height' => $dto->ceilingHeightValue,
                'price_type'     => 'за объект',
                'price'          => $dto->price ? (int) $dto->price : null,
                'currency'       => DimRiaMappings::mapCurrency($dto->currencySymbol),
                'offer_type'     => 'от посредника',
            ],
            DimRiaMappings::mapHeatingFields($dto->heatingTypeName),
            DimRiaMappings::mapFeatures($dto->featureNames),
        );
    }

    private function characteristicsLand(PropertyExportData $dto): array
    {
        $plotArea = $dto->areaLand ?: $dto->areaTotal;

        return [
            'plot_area'      => $plotArea,
            'plot_area_unit' => $plotArea ? 'сотка' : null,
            'price_type'     => 'за участок',
            'price'          => $dto->price ? (int) $dto->price : null,
            'currency'       => DimRiaMappings::mapCurrency($dto->currencySymbol),
            'offer_type'     => 'от посредника',
        ];
    }

    private function characteristicsCommercial(PropertyExportData $dto): array
    {
        return array_merge(
            [
                'total_area'                    => $dto->areaTotal,
                'rooms_count'                   => $dto->roomCountValue,
                'floors'                        => $dto->floorsTotal,
                'floor'                         => $dto->floor,
                'ceiling_height'                => $dto->ceilingHeightValue,
                'condition_of_building_repair'  => DimRiaMappings::mapCommercialCondition($dto->conditionName),
                'price_type'                    => 'за объект',
                'price'                         => $dto->price ? (int) $dto->price : null,
                'currency'                      => DimRiaMappings::mapCurrency($dto->currencySymbol),
                'offer_type'                    => 'от посредника',
            ],
            DimRiaMappings::mapHeatingFields($dto->heatingTypeName),
            DimRiaMappings::mapFeatures($dto->featureNames),
        );
    }

    private function characteristicsGarage(PropertyExportData $dto): array
    {
        return [
            'total_area'  => $dto->areaTotal,
            'price_type'  => 'за объект',
            'price'       => $dto->price ? (int) $dto->price : null,
            'currency'    => DimRiaMappings::mapCurrency($dto->currencySymbol),
            'offer_type'  => 'от посредника',
        ];
    }
}
