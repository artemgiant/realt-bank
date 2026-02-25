<?php

namespace App\Services\XmlExport\Adapters;

use App\Services\XmlExport\Dto\PropertyExportData;
use App\Services\XmlExport\Mappings\RemMappings;
use Spatie\ArrayToXml\ArrayToXml;

class RemAdapter extends AbstractXmlAdapter
{
    private const BASE_URL = 'https://rem.ua/object/base/apartments/';

    public function getName(): string
    {
        return 'rem';
    }

    public function getRootElement(): string
    {
        return 'offer';
    }

    public function toArray(PropertyExportData $dto): array
    {
        $address = $this->buildAddress($dto->streetName, $dto->buildingNumber);

        $data = [
            '_attributes' => ['internal-id' => $dto->id],
            'url'           => self::BASE_URL . $dto->id,
            'type'          => RemMappings::mapDealType($dto->dealTypeName),
            'title'         => RemMappings::buildTitle($dto->dealTypeName, $dto->propertyTypeName),
            'description'   => $dto->description,
            'property-type' => 'жилая',
            'category'      => RemMappings::mapCategory($dto->propertyTypeName),
            'creation-date' => $dto->createdAt,
            'update-time'   => $dto->updatedAt,

            'location' => [
                'country'            => $dto->countryName,
                'region'             => $dto->stateName,
                'locality-name'      => $dto->cityName,
                'district'           => $dto->districtName,
                'address'            => $address,
                'apartment'          => $dto->apartmentNumber,
                'sub-locality-name'  => $dto->zoneName,
                'longitude'          => $dto->longitude,
                'latitude'           => $dto->latitude,
            ],

            'sales-agent' => [
                'name'  => $dto->employeeName,
                'phone' => $dto->phone,
                'id'    => $dto->employeeId,
            ],

            'price' => [
                'value'    => $dto->price ? (int) $dto->price : null,
                'currency' => RemMappings::mapCurrency($dto->currencyCode),
            ],

            'area' => [
                'value' => $dto->areaTotal,
            ],

            'living-space' => [
                'value' => $dto->areaLiving,
            ],

            'kitchen-space' => [
                'value' => $dto->areaKitchen,
            ],

            'lot-area' => [
                'value' => $dto->areaLand,
            ],

            'image'        => $dto->photoUrls,
            'rooms'        => $dto->roomCountValue ? (int) $dto->roomCountValue : null,
            'floor'        => $dto->floor,
            'floor_count'  => $dto->floorsTotal,
            'is_premium'   => 'false',
            'quality'      => $dto->conditionName,
            'video-review' => $dto->youtubeUrl,
        ];

        return $data;
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
                'generation-date' => now()->format('d.m.Y H:i'),
                'offer'           => $batchData,
            ],
            [
                'rootElementName' => 'realty-feed',
                '_attributes' => [
                    'xmlns' => 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06',
                ],
            ],
            xmlEncoding: 'UTF-8',
        );
    }

    private function buildAddress(?string $streetName, ?string $buildingNumber): ?string
    {
        $parts = array_filter([$streetName, $buildingNumber]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
