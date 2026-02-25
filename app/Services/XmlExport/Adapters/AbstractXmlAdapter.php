<?php

namespace App\Services\XmlExport\Adapters;

use App\Services\XmlExport\Contracts\XmlAdapterInterface;
use App\Services\XmlExport\Dto\PropertyExportData;
use Spatie\ArrayToXml\ArrayToXml;

abstract class AbstractXmlAdapter implements XmlAdapterInterface
{
    public function validate(PropertyExportData $dto): array
    {
        return [];
    }

    public function generateXml(PropertyExportData $dto): string
    {
        $data = $this->filterEmpty($this->toArray($dto));

        return ArrayToXml::convert($data, $this->getRootElement(), xmlEncoding: 'UTF-8');
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
            [$this->getRootElement() => $batchData],
            'feed',
            xmlEncoding: 'UTF-8',
        );
    }

    /**
     * Recursively remove null, empty strings, and empty arrays from data
     */
    protected function filterEmpty(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $filtered = $this->filterEmpty($value);
                if (!empty($filtered)) {
                    $result[$key] = $filtered;
                }
            } elseif ($value !== null && $value !== '') {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
